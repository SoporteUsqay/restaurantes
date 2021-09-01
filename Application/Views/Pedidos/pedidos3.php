Skip to content
 

All gists
GitHub
Sign up for a GitHub account Sign in
@manvinymanviny/calcbootstrap
Created 6 months ago
 Code
 Revisions 1
Embed URL
	
HTTPS clone URL
	
You can clone with  HTTPS or Subversion. 
 Clone in Desktop
 Download ZIP
calculadora bootstrap
Raw  calcbootstrap
<!DOCTYPE html>
<html>
  <head>
    <title>Maciej Mensfeld - Calculator</title>

    <link rel="stylesheet" href="./assets/stylesheets/bootstrap.min.css" />
    <link rel="stylesheet" href="./assets/stylesheets/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="./assets/stylesheets/application.css" />

    <script src="./assets/javascripts/jquery-1.9.1.js"></script>
    <script src="./assets/javascripts/calculator.js"></script>
  </head>
  <body>

    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="./index.html">Bootstrap Calculator</a>
        </div>
      </div>
    </div>


    <div class="container">

      <div class="hero-unit" id="calculator-wrapper">
        <div class="row-fluid">
          <div class="span8">
            <div id="calculator-screen" class="uneditable-input"></div>
          </div>
       
          <div class="span3">
            <div id="calculator-result"  class="uneditable-input">0</div>
          </div>
        </div>

      </div>

      <div class="row-fluid">

        <div class="span6 well">
          <div id="calc-board">
            <div class="row-fluid">
             
              <a href="#" class="btn btn-danger" data-method="reset">C</a>
            </div>
            <div class="row-fluid">
              <a href="#" class="btn">7</a>
              <a href="#" class="btn">8</a>
              <a href="#" class="btn">9</a>
              
            </div>
            <div class="row-fluid">
              <a href="#" class="btn">4</a>
              <a href="#" class="btn">5</a>
              <a href="#" class="btn">6</a>
             
            </div>
            <div class="row-fluid">
              <a href="#" class="btn">1</a>
              <a href="#" class="btn">2</a>
              <a href="#" class="btn">3</a>
             
            </div>
            <div class="row-fluid">
              <a href="#" class="btn">.</a>
              <a href="#" class="btn">0</a>
              
            </div>
          </div>
        </div>

        
        <hr>

      </div>
    </div>

  </body>
</html>
Sign up for free to join this conversation on GitHub. Already have an account? Sign in to comment
Status API Training Shop Blog About Pricing
© 2015 GitHub, Inc. Terms Privacy Security Contact Help