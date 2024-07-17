<?php

namespace App\Service;

// Parameters from config files
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

// Guzzle
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class RandomUserApiService
{
    private $client;
    private $importApiUrl;
    private $limit;
    private $filters;

    /**
     * @param $importApiUrl
     * @param $limit
     * @param $filters
     */
    public function __construct(ParameterBagInterface $params)
    {
        $this->client = new Client();
        $randomUserApiParameters = $params->get('randomuser_api');

        $this->importApiUrl = $randomUserApiParameters['base_url'];
        $this->limit = $randomUserApiParameters['default_results_limit'];
        $this->filters = $randomUserApiParameters['default_filters'];
    }

    public function get($limit = "", $nationality = ""): array
    {
        if ($limit != "") {
            $this->limit = $limit;
        }

        if ($nationality != "") {
            $this->filters['nat'] = $nationality;
        }

        $query = [
            'results' => $this->limit,
        ];

        foreach ($this->filters as $name => $filter) {
            if ($name == "exc") {
                $query = array_merge([$name => implode(',', $filter)], $query);
            } else {
                $query = array_merge([$name => $filter], $query);
            }
        }

        try {
            $response = $this->client->request('GET', $this->importApiUrl, ['query' => $query]);
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['results'] ?? [];
        } catch (GuzzleException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
}