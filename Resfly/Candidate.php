<?php
//
// Candidate.php
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

class Candidate
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $resumeUrl;

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
        $this->id = $data['candidate']['id'];
        $this->resumeUrl = $data['candidate']['resume_url'];
        $this->setFirstName($data['candidate']['first_name'])
             ->setLastName($data['candidate']['last_name'])
             ->setEmail($data['candidate']['email'])
             ->setSource($data['candidate']['source'])
             ->setCity($data['candidate']['city'])
             ->setState($data['candidate']['state']);
    }

    /**
     * Get array representation
     *
     * @return array
     */
    protected function toArray()
    {
        return array('candidate' => array(
            'id' => $this->getId(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'email' => $this->getEmail(),
            'source' => $this->getSource(),
            'city' => $this->getCity(),
            'state' => $this->getState()
        ));
    }

    /**
     * Get the candidate's unique ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the candidate's first name
     *
     * @param string $firstName
     * @return Candidate Provides a fluent interface
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Get the candidate's first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the candidate's last name
     *
     * @param string $lastName
     * @return Candidate Provides a fluent interface
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Get the candidate's last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the candidate's email
     *
     * @param string $email
     * @return Candidate Provides a fluent interface
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get the candidate's email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the candidate's source
     *
     * @param string $source
     * @return Candidate Provides a fluent interface
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get the candidate's source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the candidate's city
     *
     * @param string $city
     * @return Candidate Provides a fluent interface
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get the candidate's city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the candidate's state
     *
     * @param string $state
     * @return Candidate Provides a fluent interface
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get the candidate's state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Save all changes to the candidate
     *
     * @return bool
     */
    public function save()
    {
        if (empty($this->id))
        {
            return false;
        }

        $response = $this->resflyApi->makeRequest('/candidates/'.$this->getId(), 'PUT', $this->toArray());

        if ($response->getStatusCode() !== 200)
        {
            return false;
        }

        $this->setFromApiData($response->getData());

        return true;
    }

    /**
     * Delete the candidate
     *
     * @return bool
     */
    public function delete()
    {
        $response = $this->resflyApi->makeRequest('/candidates/'.$this->getId(), 'DELETE');

        if ($response->getStatusCode() !== 204)
        {
            return false;
        }

        return true;
    }

    /**
     * Get all jobs the candidate has applied to
     *
     * @return array
     */
    public function getJobs()
    {
        $response = $this->resflyApi->makeRequest('/candidates/'.$this->getId().'/jobs', 'GET');

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