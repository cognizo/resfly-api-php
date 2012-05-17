<?php
//
// ResflyApi.php
//
// Author:
//       Graham Floyd <gfloyd@resfly.com>
//
// Copyright (c) 2012 Resfly, Inc.
//
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.

namespace Resfly;

require_once 'Response.php';
require_once 'Company.php';
require_once 'Job.php';
require_once 'Candidate.php';

class ResflyApi
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var array
     */
    protected $errors;

    public function __construct($url, $apiKey)
    {
        $this->url = $url;
        $this->apiKey = $apiKey;
    }

    /**
     * Make a request to the API
     *
     * @param string $uri
     * @param string $method
     * @param array|null $data
     * @return Response
     */
    public function makeRequest($uri, $method, array $data = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url.$uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            'X-Api-Key: '.$this->apiKey,
            'Accept: application/json'
        );

        switch ($method)
        {
            case 'POST':
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;

            case 'PUT':
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;

            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $responseBody = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $data = json_decode($responseBody, true);
        if (!empty($data['errors']))
        {
            foreach ($data['errors'] as $error)
            $this->errors[] = $error['error'];
        }

        $response = new Response($responseBody, $statusCode, $data);

        return $response;
    }

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get a company
     *
     * @param int $id
     * @return Company
     */
    public function getCompany($id)
    {
        $response = $this->makeRequest('/companies/'.$id, 'GET');

        if ($response->getStatusCode() === 200)
        {
            $company = new Company($this, $response->getData());
            return $company;
        }
    }

    /**
     * Get all companies
     *
     * @return array
     */
    public function getCompanies()
    {
        $response = $this->makeRequest('/companies', 'GET');

        $companies = array();
        if ($response->getStatusCode() === 200)
        {
            $data = $response->getData();
            foreach ($data['companies'] as $companyResponse)
            {
                $company = new Company($this, $companyResponse);
                $companies[] = $company;
            }
        }

        return $companies;
    }

    /**
     * Get a job
     *
     * @param int $id
     * @return Job
     */
    public function getJob($id)
    {
        $response = $this->makeRequest('/jobs/'.$id, 'GET');

        if ($response->getStatusCode() === 200)
        {
            $job = new Job($this, $response->getData());
            return $job;
        }
    }

    /**
     * Get all jobs
     *
     * @return array
     */
    public function getJobs()
    {
        $response = $this->makeRequest('/jobs', 'GET');

        $jobs = array();
        if ($response->getStatusCode() === 200)
        {
            $data = $response->getData();
            foreach ($data['jobs'] as $jobResponse)
            {
                $job = new Job($this, $jobResponse);
                $jobs[] = $job;
            }
        }

        return $jobs;
    }

    /**
     * Get a candidate
     *
     * @param int $id
     * @return Candidate
     */
    public function getCandidate($id)
    {
        $response = $this->makeRequest('/candidates/'.$id, 'GET');

        if ($response->getStatusCode() === 200)
        {
            $candidate = new Candidate($this, $response->getData());
            return $candidate;
        }
    }

    /**
     * Get all candidates
     *
     * @return array
     */
    public function getCandidates()
    {
        $response = $this->makeRequest('/candidates', 'GET');

        $candidates = array();
        if ($response->getStatusCode() === 200)
        {
            $data = $response->getData();
            foreach ($data['candidates'] as $candidateResponse)
            {
                $candidate = new Candidate($this, $candidateResponse);
                $candidates[] = $candidate;
            }
        }

        return $candidates;
    }
}