# Google Compute Engine PHP Sample Application

## Description
This is a simple web-based example of calling the Google Compute Engine API
in PHP.

## Prerequisites:
Please make sure that all of the following is installed before trying to run
the sample application.

- PHP 5.2.x or higher [http://www.php.net/]
- PHP Curl extension [http://www.php.net/manual/en/intro.curl.php]
- PHP JSON extension [http://php.net/manual/en/book.json.php]
- The google-api-php-client library checked out locally
  [https://code.google.com/p/google-api-php-client/] 

## Setup Authentication
NOTE: This README assumes that you have enabled access to the Google Compute
Engine API via the Developer Console page.

1) Visit https://cloud.google.com/console/ to register your
application.
- Select or create a project
- Click on "APIs & auth" in the left column
- Choose the "Credentials" sub-menu option. "Create a new client ID" if you have not
generated any client IDs, or don't want to use one you have already created. 
- Select "Web application" as the "Application type"
- Under the redirect URI, enter the location of your application
- Click "Create client ID"
- Note the client id and client secret that was just created
- Click on "Overview" in the left column and note the Project ID

2) Update app.php with the redirect uri, client id, client secret, and Project ID
obtained in step 1.
- Update 'YOUR_CLIENT_ID' with your oauth2 client id.
- Update 'YOUR_CLIENT_SECRET' with your oauth2 client secret.
- Update 'YOUR_REDIRECT_URI' with the fully qualified
redirect URI.
- Update 'YOUR_GOOGLE_COMPUTE_ENGINE_PROJECT' with your Project ID from the 
API Console.

## Running the Sample Application
3) Load app.php on your web server, and visit the appropriate website in
your web browser.
