<?php
namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class RandomUserApiService
{
    private Client $client;
    private mixed $importApiUrl;
    private mixed $limit;
    private mixed $filters;

    /**
     * @param ParameterBagInterface $params
     */
    public function __construct(ParameterBagInterface $params)
    {
        $this->client = new Client();
        $randomUserApiParameters = $params->get('randomuserapi');
        $this->importApiUrl = $randomUserApiParameters['api_url'];
        $this->limit = $randomUserApiParameters['default_results_limit'];
        $this->filters = $randomUserApiParameters['default_filters'];
    }

    /**
     * @throws Exception
     */
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
            throw new Exception($e->getMessage(), 500);
        }
    }
}