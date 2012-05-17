<?php
//
// Job.php
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

class Job
{
    const CATEGORY_ACCOUNTING_FINANCE         = 'accounting_finance';
    const CATEGORY_ADMINISTRATIVE             = 'administrative';
    const CATEGORY_ARCHITECTURE_ENGINEERING   = 'architecture_engineering';
    const CATEGORY_ART_MEDIA_DESIGN           = 'art_media_design';
    const CATEGORY_BANKING_LOANS              = 'banking_loans';
    const CATEGORY_BIOTECH_PHARMACEUTICAL     = 'biotech_pharmaceutical';
    const CATEGORY_COMPUTER_SOFTWARE          = 'computer_software';
    const CATEGORY_CONSTRUCTION_FACILITIES    = 'construction_facilities';
    const CATEGORY_CUSTOMER_SERVICE           = 'customer_service';
    const CATEGORY_EDUCATION                  = 'education';
    const CATEGORY_GENERAL_LABOR              = 'general_labor';
    const CATEGORY_GOVERNMENT_MILITARY        = 'government_military';
    const CATEGORY_HEALTHCARE                 = 'healthcare';
    const CATEGORY_HOSPITALITY_TRAVEL         = 'hospitality_travel';
    const CATEGORY_HUMAN_RESOURCES            = 'human_resources';
    const CATEGORY_INFORMATION_TECHNOLOGY     = 'information_technology';
    const CATEGORY_LAW_ENFORCEMENT_SECURITY   = 'law_enforcement_security';
    const CATEGORY_LEGAL                      = 'legal';
    const CATEGORY_MARKETING_PUBLIC_RELATIONS = 'marketing_public_relations';
    const CATEGORY_REAL_ESTATE                = 'real_estate';
    const CATEGORY_RESTAURANT_FOOD_SERVICE    = 'restaurant_food_service';
    const CATEGORY_RETAIL                     = 'retail';
    const CATEGORY_SALES                      = 'sales';
    const CATEGORY_SCIENCE_RESEARCH           = 'science_research';
    const CATEGORY_TELECOMMUNICATIONS         = 'telecommunications';
    const CATEGORY_TRANSPORTATION_LOGISTICS   = 'transportation_logistics';
    const CATEGORY_VOLUNTEERING_NON_PROFIT    = 'volunteering_non_profit';
    const CATEGORY_WRITING_EDITING            = 'writing_editing';

    const TYPE_FULL_TIME  = 'full_time';
    const TYPE_PART_TIME  = 'part_time';
    const TYPE_CONTRACT   = 'contract';
    const TYPE_INTERNSHIP = 'internship';
    const TYPE_TEMP       = 'temp';
    const TYPE_VOLUNTEER  = 'volunteer';

    const SALARY_TYPE_YEARLY = 'yearly';
    const SALARY_TYPE_HOURLY = 'hourly';

    const STATUS_DRAFT     = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_CLOSED    = 'closed';

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
    protected $companyId;

    /**
     * @var int
     */
    protected $dateCreated;

    /**
     * @var string
     */
    protected $title;

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
    protected $category;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $salary;

    /**
     * @var string
     */
    protected $detailUrl;

    /**
     * @var string
     */
    protected $applicationUrl;

    /**
     * @var string
     */
    protected $status;

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
        $this->id = $data['job']['id'];
        $this->companyId = $data['job']['company_id'];
        $this->dateCreated = strtotime($data['job']['date_created']);
        $this->status = $data['job']['status'];
        $this->setTitle($data['job']['title'])
             ->setCity($data['job']['city'])
             ->setState($data['job']['state'])
             ->setCategory($data['job']['category'])
             ->setDescription($data['job']['description'])
             ->setType($data['job']['type'])
             ->setSalary($data['job']['salary'])
             ->setDetailUrl($data['job']['detail_url'])
             ->setApplicationUrl($data['job']['application_url']);
    }

    /**
     * Get array representation
     *
     * @return array
     */
    protected function toArray()
    {
        return array('job' => array(
            'id' => $this->getId(),
            'company_id' => $this->getCompanyId(),
            'title' => $this->getTitle(),
            'city' => $this->getCity(),
            'state' => $this->getState(),
            'category' => $this->getCategory(),
            'description' => $this->getDescription(),
            'type' => $this->getType(),
            'salary' => $this->getSalary(),
            'detail_url' => $this->getDetailUrl(),
            'application_url' => $this->getApplicationUrl()
        ));
    }

    /**
     * Get the job's unique ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the job's company ID
     *
     * @param int $companyId
     * @return Job Provides a fluent interface
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * Get job's company ID
     *
     * @return int
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Get the job's date created
     *
     * @return int Unix timestamp
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set the job's title
     *
     * @param string $title
     * @return Job Provides a fluent interface
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the job's title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the job's city
     *
     * @param string $city
     * @return Job Provides a fluent interface
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get the job's city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the job's state
     *
     * @param string $state
     * @return Job Provides a fluent interface
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get the job's state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the job's category
     *
     * @param string $category
     * @return Job Provides a fluent interface
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get the job's category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set the job's description
     *
     * @param string $description
     * @return Job Provides a fluent interface
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the job's description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the jobs' type
     *
     * @param string $type
     * @return Job Provides a fluent interface
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the job's type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the job's salary
     *
     * @param array $salary
     * @return Job Provides a fluent interface
     */
    public function setSalary(array $salary)
    {
        $this->salary = $salary;
        return $this;
    }

    /**
     * Get the job's salary
     *
     * @return array
     */
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     * Set the job's detail URL
     *
     * @param string $detailUrl
     * @return Job Provides a fluent interface
     */
    public function setDetailUrl($detailUrl)
    {
        $this->detailUrl = $detailUrl;
        return $this;
    }

    /**
     * Get the job's detail URL
     *
     * @return string
     */
    public function getDetailUrl()
    {
        return $this->detailUrl;
    }

    /**
     * Set the job's application URL
     *
     * @param string $applicationUrl
     * @return Job Provides a fluent interface
     */
    public function setApplicationUrl($applicationUrl)
    {
        $this->applicationUrl = $applicationUrl;
        return $this;
    }

    /**
     * Get the job's application URL
     *
     * @return string
     */
    public function getApplicationUrl()
    {
        return $this->applicationUrl;
    }

    /**
     * Get the job's status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Save all changes to the job
     *
     * @return bool
     */
    public function save()
    {
        $response = null;

        if (empty($this->id))
        {
            $response = $this->resflyApi->makeRequest('/jobs', 'POST', $this->toArray());

            if ($response->getStatusCode() !== 201)
            {
                return false;
            }
        }
        else
        {
            $response = $this->resflyApi->makeRequest('/jobs/'.$this->getId(), 'PUT', $this->toArray());

            if ($response->getStatusCode() !== 200)
            {
                return false;
            }
        }

        $this->setFromApiData($response->getData());

        return true;
    }

    /**
     * Delete the job
     *
     * @return bool
     */
    public function delete()
    {
        $response = $this->resflyApi->makeRequest('/jobs/'.$this->getId(), 'DELETE');

        if ($response->getStatusCode() !== 204)
        {
            return false;
        }

        return true;
    }

    /**
     * Publish the job
     *
     * @return bool
     */
    public function publish()
    {
        $response = $this->resflyApi->makeRequest('/jobs/'.$this->getId().'/publish', 'PUT');

        if ($response->getStatusCode() !== 200)
        {
            return false;
        }

        $this->status = self::STATUS_PUBLISHED;

        return true;
    }

    /**
     * Close the job
     *
     * @return bool
     */
    public function close()
    {
        $response = $this->resflyApi->makeRequest('/jobs/'.$this->getId().'/close', 'PUT');

        if ($response->getStatusCode() !== 200)
        {
            return false;
        }

        $this->status = self::STATUS_CLOSED;

        return true;
    }

    /**
     * Get all candidates for the job
     *
     * @return array
     */
    public function getCandidates()
    {
        if (empty($this->id))
        {
            return false;
        }

        $response = $this->resflyApi->makeRequest('/jobs/'.$this->getId().'/candidates', 'GET');

        $candidates = array();
        if ($response->getStatusCode() === 200)
        {
            $data = $response->getData();
            foreach ($data['candidates'] as $candidateResponse)
            {
                $candidate = new Candidate($this->resflyApi, $candidateResponse);
                $candidates[] = $candidate;
            }
        }

        return $candidates;
    }
}