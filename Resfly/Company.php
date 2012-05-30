<?php
//
// Company.php
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

require_once 'ResflyApi.php';

class Company
{
    const TYPE_INTERNAL = 'internal';
    const TYPE_AGENCY   = 'agency';

    /**
     * @var ResflyApi
     */
    protected $resflyApi;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $dateCreated;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var int
     */
    protected $jobSlots;

    /**
     * @var int
     */
    protected $jobSlotsUsed;

    /**
     * @var int
     */
    protected $jobSlotsAvailable;

    /**
     * Constructor
     *
     * @param ResflyApi $resflyApi
     * @param array|null $data
     */
    public function __construct(ResflyApi $resflyApi, array $data = null)
    {
        $this->resflyApi = $resflyApi;

        if (!empty($data))
        {
            $this->setFromApiData($data);
        }
    }

    /**
     * Set object's values from API data array
     *
     * @param array $data
     */
    protected function setFromApiData(array $data)
    {
        $this->id = $data['company']['id'];
        $this->dateCreated = strtotime($data['company']['date_created']);
        $this->jobSlotsUsed = $data['company']['job_slots_used'];
        $this->jobSlotsAvailable = $data['company']['job_slots_available'];
        $this->setName($data['company']['name'])
            ->setType($data['company']['type'])
            ->setUrl($data['company']['url'])
            ->setJobSlots($data['company']['job_slots']);
    }

    /**
     * Get array representation
     *
     * @return array
     */
    protected function toArray()
    {
        return array('company' => array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'url' => $this->getUrl(),
            'job_slots' => $this->getJobSlots()
        ));
    }

    /**
     * Get the company's unique ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the company's date created
     *
     * @return int Unix timestamp
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set the company's name
     *
     * @param string $name
     * @return Company Provides a fluent interface
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the company's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the company's type
     *
     * @param string $type
     * @return Company Provides a fluent interface
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the company's type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the company's URL
     *
     * @param string $url
     * @return Company Provides a fluent interface
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get the company's URL
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the company's job slots
     *
     * @param int $jobSlots
     * @return Company Provides a fluent interface
     */
    public function setJobSlots($jobSlots)
    {
        $this->jobSlots = $jobSlots;
        return $this;
    }

    /**
     * Get the company's job slots
     *
     * @return int
     */
    public function getJobSlots()
    {
        return $this->jobSlots;
    }

    /**
     * Get the number of job slots currently in use for the company
     *
     * @return int
     */
    public function getJobSlotsUsed()
    {
        return $this->jobSlotsUsed;
    }

    public function getJobSlotsAvailable()
    {
        return $this->jobSlotsAvailable;
    }

    /**
     * Save all changes to the company
     *
     * @return bool
     */
    public function save()
    {
        $response = null;

        if (empty($this->id))
        {
            $response = $this->resflyApi->makeRequest('/companies', 'POST', $this->toArray());

            if ($response->getStatusCode() !== 201)
            {
                return false;
            }
        }
        else
        {
            $response = $this->resflyApi->makeRequest('/companies/'.$this->getId(), 'PUT', $this->toArray());

            if ($response->getStatusCode() !== 200)
            {
                return false;
            }
        }

        $this->setFromApiData($response->getData());

        return true;
    }

    /**
     * Delete the company
     *
     * @return bool
     */
    public function delete()
    {
        $response = $this->resflyApi->makeRequest('/companies/'.$this->getId(), 'DELETE');

        if ($response->getStatusCode() === 204)
        {
            return true;
        }

        return false;
    }

    /**
     * Suspend the company
     *
     * @return bool
     */
    public function suspend()
    {
        $response = $this->resflyApi->makeRequest('/companies/'.$this->getId().'/suspend', 'PUT');

        if ($response->getStatusCode() === 200)
        {
            return true;
        }

        return false;
    }

    /**
     * Get all jobs for the company
     *
     * @return array
     */
    public function getJobs()
    {
        $response = $this->resflyApi->makeRequest('/companies/'.$this->getId().'/jobs', 'GET');

        $jobs = array();
        if ($response->getStatusCode() === 200)
        {
            $data = $response->getData();
            foreach ($data['jobs'] as $jobResponse)
            {
                $job = new Job($this->resflyApi, $jobResponse);
                $jobs[] = $job;
            }
        }

        return $jobs;
    }
}