<?php
/*
 * Copyright 2012-2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Follow the instructions on https://github.com/google/google-api-php-client/
 * to download, extract, and include the Google APIs client library for PHP into
 * your project.
 */
require_once 'Google/Client.php';
require_once 'Google/Service/Compute.php';

session_start();

/**
 * Visit https://cloud.google.com/console to generate your
 * oauth2_client_id, oauth2_client_secret, and to register your
 * oauth2_redirect_uri.
 */
$client = new Google_Client();
$client->setApplicationName('Google Compute Engine PHP Starter Application');
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');

/**
 * Create a service handle for the Google Compute Engine API.
 */
$client->setRedirectUri('YOUR_REDIRECT_URI');
$computeService = new Google_Service_Compute($client);
$client->addScope(Google_Service_Compute::COMPUTE);
$client->addScope(Google_Service_Compute::DEVSTORAGE_FULL_CONTROL);

/**
 * The name of your Google Compute Engine Project.
 */
$project = 'YOUR_GOOGLE_COMPUTE_ENGINE_PROJECT';

/**
 * Constants for sample request parameters.  */
define('API_VERSION', 'v1');
define('BASE_URL', 'https://www.googleapis.com/compute/'. API_VERSION . 
  '/projects/');
define('DEFAULT_PROJECT', $project);
define('DEFAULT_NAME', 'new-node');
define('DEFAULT_ZONE_NAME', 'us-central1-a');
define('DEFAULT_ZONE', BASE_URL . DEFAULT_PROJECT . '/zones/' . 
  DEFAULT_ZONE_NAME);
define('DEFAULT_MACHINE_TYPE', BASE_URL . DEFAULT_PROJECT . '/zones/' . 
  DEFAULT_ZONE_NAME . '/machineTypes/n1-standard-1');
define('DEFAULT_IMAGE', BASE_URL . 'debian-cloud' .  
  '/global/images/backports-debian-7-wheezy--v20131127');
define('DEFAULT_NETWORK', BASE_URL . DEFAULT_PROJECT .
  '/global/networks/default');

/**
 * Generates the markup for a specific Google Compute Engine API request.
 * @param string $apiRequestName The name of the API request to process.
 * @param string $apiResponse The API response to process.
 * @return string Markup for the specific Google Compute Engine API request.
 */
function generateMarkup($apiRequestName, $apiResponse) {
  $apiRequestMarkup = '';
  $apiRequestMarkup .= "<header><h2>" . $apiRequestName . "</h2></header>";

  if ($apiResponse['items'] == '' ) {
    $apiRequestMarkup .= "<pre>";
    $apiRequestMarkup .= print_r(json_decode(json_encode($apiResponse), true), true);
    $apiRequestMarkup .= "</pre>";
  } else {
    foreach($apiResponse['items'] as $response) {
      $apiRequestMarkup .= "<pre>";
      $apiRequestMarkup .= print_r(json_decode(json_encode($response), true), true);
      $apiRequestMarkup .= "</pre>";
    }
  }

  return $apiRequestMarkup;
}

/**
 * Queries the Google Compute Engine API to determine if the zone operation has
 * completed.
 * @param Google_Service_Compute $computeService The service handle used to
 *        make Google Compute Engine API calls. 
 * @param string $project The project the operation is executing within.
 * @param string $zone The The zone the operation is executing within.
 * @param string $operation The auto-generated name of the operation instance.
 * @return 0 = success, 1 = error.
 */
function waitForZoneOperationCompletion($computeService, $project, $zone, 
    $operation) {
  for ($x=0; $x<=20; $x++) {
    $operationStatus = $computeService->zoneOperations->get($project,
      $zone, $operation);
    if ("DONE"==$operationStatus->getStatus()) {
      return 0; 
    }
    sleep((2*$x)); 
  }
  return 1; 
}

/**
 * Clear access token whenever a logout is requested.
 */
if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
}

/**
 * Authenticate and set client access token.
 */
if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

/**
 * Set client access token.
 */
if (isset($_SESSION['access_token'])) {
  $client->setAccessToken($_SESSION['access_token']);
}

/**
 * If all authentication has been successfully completed, make Google Compute
 * Engine API requests.
 */
if ($client->getAccessToken()) {
  /**
   * Google Compute Engine API request to retrieve the list of instances in your
   * Google Compute Engine project.
   */
  $instances = $computeService->instances->listInstances(DEFAULT_PROJECT,
    DEFAULT_ZONE_NAME);
  $instancesListMarkup = generateMarkup('List Instances', $instances);

  /**
   * Google Compute Engine API request to retrieve the list of all data center
   * locations associated with your Google Compute Engine project.
   */
  $zones = $computeService->zones->listZones(DEFAULT_PROJECT);
  $zonesListMarkup = generateMarkup('List Zones', $zones);

  /**
   * Google Compute Engine API request to retrieve the list of all machine types
   * associated associated with your Google Compute Engine project.
   */
  $machineTypes = $computeService->machineTypes->listMachineTypes(DEFAULT_PROJECT,
    DEFAULT_ZONE_NAME);
  $machineTypesListMarkup = generateMarkup('List Machine Types',
    $machineTypes);

  /**
   * Google Compute Engine API request to retrieve the list of all image types
   * created in your Google Compute Engine project.
   */
  $images = $computeService->images->listImages(DEFAULT_PROJECT);
  $imagesListMarkup = generateMarkup('Project Images', $images);

  /**
   * Google Compute Engine API request to retrieve the list of popular images
   * available to your Google Compute Engine project.
   */
  $images = $computeService->images->listImages('debian-cloud');
  $imagesListMarkup2 = generateMarkup('Debian Standard Images', $images);
  $images = $computeService->images->listImages('centos-cloud');
  $imagesListMarkup3 = generateMarkup('Centos Standard Images', $images);

  /**
   * Google Compute Engine API request to retrieve the list of all firewalls
   * associated associated with your Google Compute Engine project.
   */
  $firewalls = $computeService->firewalls->listFirewalls(DEFAULT_PROJECT);
  $firewallsListMarkup = generateMarkup('List Firewalls', $firewalls);

  /**
   * Google Compute Engine API request to retrieve the list of all networks
   * associated associated with your Google Compute Engine project.
   */
  $networks = $computeService->networks->listNetworks(DEFAULT_PROJECT);
  $networksListMarkup = generateMarkup('List Networks', $networks);;

  /**
   * Google Compute Engine API request to insert a persistent disk into your
   * Google Compute Engine project. This will be used to boot an instance.
   */
  $name = DEFAULT_NAME;
  $machineType = DEFAULT_MACHINE_TYPE;
  $zone = DEFAULT_ZONE_NAME;
  $image = DEFAULT_IMAGE;
  $network = DEFAULT_NETWORK;

  $new_disk = new Google_Service_Compute_Disk();
  $new_disk->setName($name);
  $new_disk->setSourceImage($image);
  $new_disk->setSizeGb("100");

  $insertDiskOperation = $computeService->disks->insert(DEFAULT_PROJECT,
    $zone, $new_disk);

  if (waitForZoneOperationCompletion($computeService, DEFAULT_PROJECT, $zone,
      $insertDiskOperation->getName())>0) {
    exit('Error inserting disk.');
  }

  $bootDisk = $computeService->disks->get(DEFAULT_PROJECT, $zone, $name);
  if (!("READY"==$bootDisk->getStatus())) {
    exit("Disk creation didn't succeed.");
  }

  $insertDiskMarkup = generateMarkup('Inserted Disk', $bootDisk);

  /**
   * Google Compute Engine API request to insert an instance into your Google
   * Compute Engine project.
   */
  $startupScriptMetadata = new Google_Service_Compute_MetadataItems();
  $startupScriptMetadata->setKey('startup-script');
  $startupScriptMetadata->setValue('apt-get install apache2 \n apt-get install mysql');

  $metadata = new Google_Service_Compute_Metadata();
  $metadata->setItems(array($startupScriptMetadata));

  $googleNetworkInterface = new Google_Service_Compute_NetworkInterface();
  $network = DEFAULT_NETWORK;
  $googleNetworkInterface->setNetwork($network);

  $primaryDisk = new Google_Service_Compute_AttachedDisk();
  $primaryDisk->setBoot("TRUE");
  $primaryDisk->setDeviceName("primary");
  $primaryDisk->setMode("READ_WRITE");
  $primaryDisk->setSource($bootDisk->getSelfLink());
  $primaryDisk->setType("PERSISTENT");

  $new_instance = new Google_Service_Compute_Instance();
  $new_instance->setName($name);
  $new_instance->setMachineType($machineType);
  $new_instance->setNetworkInterfaces(array($googleNetworkInterface));
  $new_instance->setDisks(array($bootDisk));
  $new_instance->setMetadata($metadata);
  $new_instance->setDisks(array($primaryDisk));

  $insertInstanceOperation = $computeService->instances->insert(DEFAULT_PROJECT,
    $zone, $new_instance);

  if (waitForZoneOperationCompletion($computeService, DEFAULT_PROJECT, $zone,
      $insertInstanceOperation->getName())>0) {
    exit('Error creating instance.');
  }

  $insertedInstance = $computeService->instances->get(DEFAULT_PROJECT, $zone,
    $name);

  if (!("RUNNING"==$insertedInstance->getStatus())) {
    exit("Instance creation didn't succeed.");
  }

  $insertInstanceMarkup = generateMarkup('Inserted Instance', $insertedInstance);

  /**
   * Google Compute Engine API request to delete an instance matching the
   * outlined parameters from your Google Compute Engine project.
   */
  $deleteInstanceOperation = $computeService->instances->delete(DEFAULT_PROJECT,
    $zone, $name);

  if (waitForZoneOperationCompletion($computeService, DEFAULT_PROJECT, $zone,
      $deleteInstanceOperation->getName())>0) {
    exit('Error deleting instance.');
  }

  $deleteInstanceMarkup = generateMarkup('Delete Instance Operation',
      $deleteInstanceOperation);

  /**
   * Google Compute Engine API request to delete a disk matching the
   * outlined parameters from your Google Compute Engine project.
   */
  $deleteDiskOperation = $computeService->disks->delete(DEFAULT_PROJECT, 
    $zone, $name);

  if (waitForZoneOperationCompletion($computeService, DEFAULT_PROJECT, $zone,
      $deleteDiskOperation->getName())>0) {
    error_log('Error deleting disk.');
  }

  $deleteDiskMarkup = generateMarkup('Delete Disk Operation',
    $deleteDiskOperation);

  /**
   * Google Compute Engine API request to retrieve the list of all global
   * operations associated with your Google Compute Engine project.
   */
  $globalOperations = $computeService->globalOperations->listGlobalOperations(DEFAULT_PROJECT);
  $operationsListMarkup = generateMarkup('List Global Operations', $globalOperations);

  // The access token may have been updated lazily.
  $_SESSION['access_token'] = $client->getAccessToken();
} else {
  $authUrl = $client->createAuthUrl();
}
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <header><h1>Google Compute Engine Sample App</h1></header>
    <div class="main-content">
      <?php if(isset($instancesListMarkup)): ?>
        <div id="listInstances"><?php print $instancesListMarkup ?></div>
      <?php endif ?>

      <?php if(isset($zonesListMarkup)): ?>
        <div id="listZones"><?php print $zonesListMarkup ?></div>
      <?php endif ?>

      <?php if(isset($machineTypesListMarkup)): ?>
        <div id="listMachineTypes"><?php print $machineTypesListMarkup ?></div>
      <?php endif ?>

      <?php if(isset($imagesListMarkup)): ?>
        <div id="listImages"><?php print $imagesListMarkup ?></div>
      <?php endif ?>

      <?php if(isset($imagesListMarkup2)): ?>
        <div id="listImages"><?php print $imagesListMarkup2 ?></div>
      <?php endif ?>

      <?php if(isset($imagesListMarkup3)): ?>
        <div id="listImages"><?php print $imagesListMarkup3 ?></div>
      <?php endif ?>

      <?php if(isset($firewallsListMarkup)): ?>
        <div id="listFirewalls"><?php print $firewallsListMarkup ?></div>
      <?php endif ?>

      <?php if(isset($networksListMarkup)): ?>
        <div id="listNetworks"><?php print $networksListMarkup ?></div>
      <?php endif ?>

      <?php if(isset($insertDiskMarkup)): ?>
        <div id="insertDisk"><?php print $insertDiskMarkup ?></div>
      <?php endif ?>

      <?php if(isset($insertInstanceMarkup)): ?>
        <div id="insertInstance"><?php print $insertInstanceMarkup ?></div>
      <?php endif ?>

      <?php if(isset($deleteInstanceMarkup)): ?>
        <div id="deleteInstance"><?php print $deleteInstanceMarkup ?></div>
      <?php endif ?>

      <?php if(isset($deleteDiskMarkup)): ?>
        <div id="deleteDisk"><?php print $deleteDiskMarkup ?></div>
      <?php endif ?>

      <?php if(isset($operationsListMarkup)): ?>
        <div id="listGlobalOperations"><?php print $operationsListMarkup ?></div>
      <?php endif ?>

      <?php
        if(isset($authUrl)) {
          print "<a class='login' href='$authUrl'>Connect Me!</a>";
        } else {
          print "<a class='logout' href='?logout'>Logout</a>";
        }
      ?>
    </div>
  </body>
</html>
