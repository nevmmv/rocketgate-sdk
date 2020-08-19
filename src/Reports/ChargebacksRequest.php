<?php


namespace Nevmmv\RocketGate\Reports;

class ChargebacksRequest extends AbstractRequest
{
    /**
     * @var string
     * Value | Description
     * T | Transaction Date
     * L | Chargeback Date (Bank Load Date)
     * C | ReceivedDate (default)
     * D |Chargeback Dispute (Representment Request) Date
     */
    const DATE_COLUMN_T = 'T';
    const DATE_COLUMN_D = 'D';
    const DATE_COLUMN_C = 'C';
    const DATE_COLUMN_L = 'L';

    const RETURN_FORMAT_JSON = 'JSON';
    const RETURN_FORMAT_CSV = 'CSV';
    const RETURN_FORMAT_XML = 'XML';


    public function __construct()
    {
        $this->setParam(RequestParams::METHOD, 'getChargebacks');
        $this->setTimeZone('UTC');
        $this->setDateColumn(self::DATE_COLUMN_L);
        $this->setReturnFormat(self::RETURN_FORMAT_JSON);
    }

    public function getLink(): string
    {
        return 'ChargebackQueue.cfc';
    }


    public function handleResponse(string $data): array
    {
        if ($this->getReturnFormat() === ChargebacksRequest::RETURN_FORMAT_XML) {
            $data = json_decode(json_encode(simplexml_load_string($data)), true);

            return array_map(function ($row) {
                $dateNormalizer = function ($value) {
                    $dateFormat = 'Y-m-d H:i:s.u';
                    $value = date_create_immutable_from_format($dateFormat, $value, new \DateTimeZone('UTC'));
                    return $value ? $value : null;
                };

                $row['bankloaddate'] = $dateNormalizer($row['bankloaddate']);
                $row['chargebackdate'] = $dateNormalizer($row['chargebackdate']);
                $row['chargebackrevieweddate'] = $dateNormalizer($row['chargebackrevieweddate']);
                $row['dispute_date'] = $dateNormalizer($row['dispute_date']);
                $row['refund_date'] = $dateNormalizer($row['refund_date']);
                $row['transactiondate'] = $dateNormalizer($row['transactiondate']);
                $row['tr_chargebackdate'] = $dateNormalizer(!isset($row['tr_chargebackdate']) ? null : $row['tr_chargebackdate'] . ' 00:00:00.0');


                $row['chargeback_diff'] = (int)($row['chargeback_diff']);
                $row['merchant_site_id'] = (int)($row['merchant_site_id']);
                $row['merch_id'] = (int)($row['merch_id']);
                $row['merchant_account'] = (int)($row['merchant_account']);
                $row['refund_diff'] = (int)($row['refund_diff']);

                $row['chargebackamount'] = (float)($row['chargebackamount']);
                $row['transactionamount'] = (float)($row['transactionamount']);
                foreach ($row as $k => $v) {
                    if (is_array($row[$k]) && count($row[$k]) === 0) {
                        $row[$k] = null;
                    }
                }
                return $row;
            }, $data);
        }
        if ($this->getReturnFormat() === ChargebacksRequest::RETURN_FORMAT_JSON) {
            $jsonCheck = substr($data, 0, 2);

            if ($jsonCheck === '//') {
                $json = json_decode(substr($data, 2), true);
                $keys = array_map('strtolower', $json['COLUMNS']);
                return array_map(function ($values) use ($keys) {
                    $row = array_combine($keys, array_map('trim', $values));

                    $dateNormalizer = function ($value) {
                        $dateFormat = 'F, d Y H:i:s.u';
                        $value = date_create_immutable_from_format($dateFormat, $value . '.0', new \DateTimeZone('UTC'));


                        return $value ? $value : null;
                    };

                    $row['bankloaddate'] = $dateNormalizer($row['bankloaddate']);
                    $row['chargebackdate'] = $dateNormalizer($row['chargebackdate']);
                    $row['chargebackrevieweddate'] = $dateNormalizer($row['chargebackrevieweddate']);
                    $row['dispute_date'] = $dateNormalizer($row['dispute_date']);
                    $row['refund_date'] = $dateNormalizer($row['refund_date']);
                    $row['transactiondate'] = $dateNormalizer($row['transactiondate']);
                    $row['tr_chargebackdate'] = $dateNormalizer(!isset($row['tr_chargebackdate']) ? null : $row['tr_chargebackdate']);


                    $row['chargeback_diff'] = (int)($row['chargeback_diff']);
                    $row['merchant_site_id'] = (int)($row['merchant_site_id']);
                    $row['merch_id'] = (int)($row['merch_id']);
                    $row['merchant_account'] = (int)($row['merchant_account']);
                    $row['refund_diff'] = (int)($row['refund_diff']);

                    $row['chargebackamount'] = (float)($row['chargebackamount']);
                    $row['transactionamount'] = (float)($row['transactionamount']);

                    return $row;
                }, $json['DATA']);
            }
        }
        return [];
    }
}
