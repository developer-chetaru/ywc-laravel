<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\RequestException;

class CocCheckerService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://coccec.mcga.gov.uk/',
            'timeout'  => 15.0,
            'verify'   => false, // skip SSL errors if any
            'headers'  => [
                'User-Agent' => 'Mozilla/5.0 (compatible; CocChecker/1.0; +https://yourdomain.com)'
            ],
        ]);
    }

    /**
     * Check certificate status by document number and DOB.
     *
     * @param string $docNumber
     * @param string $dob Format: YYYY-MM-DD
     * @return array
     */
    public function checkCertificate(string $docNumber, string $dob): array
    {
        try {
            $url = "Certificate-Search-Results/?documentId=" . urlencode($docNumber) . "&date=" . urlencode($dob);

            $response = $this->client->get($url);
            $html = (string) $response->getBody();

            $crawler = new Crawler($html);

            if (stripos($html, 'Certificate Record Found') !== false) {
                $certificateNumber = null;
                $details = null;

                try {
                    $certificateRow = $crawler->filterXPath('//td[contains(text(), "CoC") or contains(text(), "CoC")]')->first();
                    if ($certificateRow->count()) {
                        $certificateNumber = trim($certificateRow->parents()->text());
                    }
                } catch (\Exception $e) {
                    $certificateNumber = null; // fail-safe
                }

                try {
                    $details = trim($crawler->filter('table')->first()->text());
                } catch (\Exception $e) {
                    $details = null;
                }

                return [
                    'status' => 'found',
                    'certificate_number' => $certificateNumber ?: '',
                    'details' => $details ?: '',
                ];
            }

            return [
                'status' => 'not_found'
            ];
        } catch (RequestException $e) {
            // Log error for debugging
            \Log::error('CocCheckerService: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Unable to connect to Certificate Checking service.'
            ];
        } catch (\Exception $e) {
            \Log::error('CocCheckerService Exception: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An unexpected error occurred during certificate checking.'
            ];
        }
    }
}
