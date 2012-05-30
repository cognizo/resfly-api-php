<?php
//
// example.php
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

include 'Resfly/ResflyApi.php';

$resflyApi = new \Resfly\ResflyApi('https://api.resfly.com', 'API-KEY');

// Create a company
$company = new \Resfly\Company($resflyApi);
$company->setName('ACME, Inc.')
        ->setType(\Resfly\Company::TYPE_INTERNAL)
        ->setUrl('http://www.acmeinc.com')
        ->setJobSlots(5);

if ($company->save())
{
    echo $company->getId().PHP_EOL;
    echo $company->getDateCreated().PHP_EOL;
}

// Edit the company
$company->setName('ACME International, Inc.');

if ($company->save())
{
    echo $company->getName().PHP_EOL;
}

// Create a job
$job = new \Resfly\Job($resflyApi);
$job->setCompanyId($company->getId())
    ->setTitle('Marketing Specialist')
    ->setCity('Minneapolis')
    ->setState('MN')
    ->setCategory(\Resfly\Job::CATEGORY_MARKETING_PUBLIC_RELATIONS)
    ->setDescription('This a test description.')
    ->setType(\Resfly\Job::TYPE_FULL_TIME)
    ->setSalary(array('amount' => 50000, 'type' => \Resfly\Job::SALARY_TYPE_YEARLY));

if ($job->save())
{
    echo $job->getId().PHP_EOL;
    echo $job->getDateCreated().PHP_EOL;
}

// Publish the job
if ($job->publish())
{
    echo $job->getStatus().PHP_EOL;
}

// Get all jobs for the company
$companyJobs = $company->getJobs();
foreach ($companyJobs as $companyJob)
{
    echo $companyJob->getTitle().PHP_EOL;
}

// Get all candidates for the job
$candidates = $job->getCandidates();
foreach ($candidates as $candidate)
{
    echo $candidate->getFirstName().' '.$candidate->getLastName().PHP_EOL;
}

// Close the job
if ($job->close())
{
    echo $job->getStatus().PHP_EOL;
}

// Delete the job
$job->delete();

// Suspend the company and close all published jobs
$company->suspend();

// Error checking
$company = new \Resfly\Company($resflyApi);
if (!$company->save())
{
    foreach ($resflyApi->getErrors() as $error)
    {
        echo $error.PHP_EOL;
    }
}

