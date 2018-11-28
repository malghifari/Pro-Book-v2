<html>
<?php
  Session::start();
  if (Session::exist('message')):
?>
  <div class="info-dialog">
    <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
    <p><?php echo Session::get('message'); ?></p>
  </div>
<?php
  Session::unset('message');
  endif;
?>
<head>
  <title>Register</title>
  <link rel="stylesheet" href="/public/css/main.css" />
  <script src="/public/js/validation.js"></script>
</head>
<body>
  <div class="bg-primary container w-40 mt-25 pt-8 pb-8" style="margin-top: 12.5vh">
    <form name="registerForm" class="form-group bg-polkadot ml-4 mr-4" action="/index.php/register" method="POST" onsubmit="event.preventDefault(); return validate();">
      <h1>REGISTER</h1>
      <div class="form-control m-8 pb-24">
        <label>Name</label>
        <input name="name" class="form-control" type="text" placeholder="name" autofocus>
      </div>
      <div class="form-control m-8 pb-24">
        <label>Username</label>
        <input type="hidden" id="usernameValid">
        <input type="text" class="form-control" id="usernameField" name="username" placeholder="username">
        <img id="usernameValidity" hidden class="validity"/>
      </div>
      <div class="form-control m-8 pb-24">
        <label>Email</label>
        <input type="hidden" id="emailValid">
        <input type="text" class="form-control" id="emailField" name="email" placeholder="email">
        <img id="emailValidity" hidden class="validity"/>
      </div>
      <div class="form-control m-8 pb-24">
        <label>Password</label>
        <input type="password" class="form-control" name="password" placeholder="password">
      </div>
      <div class="form-control m-8 pb-32">
        <label>Confirm Password</label>
        <input type="password" class="form-control" name="confirmation" placeholder="confirmation">
      </div>
      <div class="form-control m-8 pb-24">
        <label>Address</label>
        <input type="text" class="form-control" name="address" type="text" placeholder="address">
      </div>
      <div class="form-control m-8 pb-32">
        <label>Phone Number</label>
        <input type="text" class="form-control" name="phone" type="text" placeholder="phone">
      </div>
      <div class="form-control m-8 pb-32">
        <label>Card Number</label>
        <input type="hidden" id="cardNumberValid">
        <input type="text" class="form-control" name="card_number" type="text" id="cardNumberField" placeholder="card number">
        <img id="cardNumberValidity" hidden class="validity"/>
      </div>
      <div class="form-control ml-24 p-8" style="text-align: left;">
        <a href="/index.php/login">Already have an account?</a>
      </div>
      <button type="submit" class="mb-16 button-secondary">REGISTER</button>
    </form>
  </div>
  <script>
    function validate() {
      let validity = (
        validation.required([
          document.registerForm.name,
          document.registerForm.username,
          document.registerForm.email,
          document.registerForm.password,
          document.registerForm.confirmation,
          document.registerForm.address,
          document.registerForm.phone,
          document.registerForm.card_number,
        ])
      );

      if (validity.result) {
        validity = (
          validation.maxLen([
            document.registerForm.username,
          ], 20)
        );
      }

      if (validity.result) {
        validity = (
          validation.email([
            document.registerForm.email,
          ])
        );
      }

      if (validity.result) {
        validity = (
          validation.phone([
            document.registerForm.phone,
          ])
        );
      }

      if (validity.result) {
        validity = (
          validation.password([
            document.registerForm.password,
            document.registerForm.confirmation,
          ])
        );
      }

      const usernameValid = document.getElementById('usernameValid').value == 1;
      const emailValid = document.getElementById('emailValid').value == 1;
      const cardNumberValid = document.getElementById('cardNumberValid').value;

      if (validity.result && usernameValid && emailValid && cardNumberValid == 1) {
        document.registerForm.submit();
      } else if (validity.message !== 'OK') {
        validation.display(validity.message);
      } else if (!usernameValid) {
        validation.display('username is already used');
      } else if (!emailValid) {
        validation.display('email is already used');
      } else if (cardNumberValid == -1) {
        validation.display('card number is already used');
      } else if (cardNumberValid == -2) {
        validation.display('card not yet registered in our bank service');
      }
    }

    document.getElementById('usernameField').onblur = function () {
      const value = this.value;
      const xhr = new XMLHttpRequest();
      const expValid = validation.required([
        document.registerForm.username,
      ]).result;
      xhr.open('GET', `/index.php/apis/validate-username?username=${value}`);
      xhr.onload = function() {
        if (xhr.status === 200) {
          const available = JSON.parse(xhr.responseText).result;
          var img = document.getElementById('usernameValidity');
          if (expValid && available) {
            document.getElementById('usernameValid').value = 1;
            img.hidden = false;
            img.src = '/public/images/svg/check-sign.svg';
          } else {
            document.getElementById('usernameValid').value = 0;
            img.hidden = false;
            img.src = '/public/images/svg/remove-symbol.svg';
          };
        } else {
          console.log('Request failed.  Returned status of ' + xhr.status);
        }
      };
      xhr.send();
    };

    document.getElementById('emailField').onblur = function () {
      const value = this.value;
      const xhr = new XMLHttpRequest();
      const expValid = validation.email([
        document.registerForm.email,
      ]).result;
      xhr.open('GET', `/index.php/apis/validate-email?email=${value}`);
      xhr.onload = function() {
        if (xhr.status === 200) {
          const available = JSON.parse(xhr.responseText).result;
          var img = document.getElementById('emailValidity');
          if (expValid && available) {
            document.getElementById('emailValid').value = 1;
            img.hidden = false;
            img.src = '/public/images/svg/check-sign.svg';
          } else {
            document.getElementById('emailValid').value = 0;
            img.hidden = false;
            img.src = '/public/images/svg/remove-symbol.svg';
          };
        } else {
          console.log('Request failed.  Returned status of ' + xhr.status);
        }
      };
      xhr.send();
    };

    function makeGetRequest(url) {
      return new Promise(function(resolve, reject) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', url);
        xhr.onload = function() {
          if (xhr.status === 200) {
            resolve(xhr.responseText);
          } else {
            reject('Request failed.  Returned status of ' + xhr.status);
          }
        }
        xhr.error = function()  {
          reject('Request failed');
        }
        xhr.send();
      });
    }

    document.getElementById('cardNumberField').onblur = function()  {
      const urlProbook = `/index.php/apis/validate-card-number?card-number=${this.value}`;
      const urlBank = `http://localhost:3000/validate-card?card_number=${this.value}`;
      const expValid = validation.required([
          document.registerForm.card_number,
        ]).result;
      if (expValid) {        
        Promise.all([makeGetRequest(urlProbook), makeGetRequest(urlBank)])
        .then((result) => {
          resultProbook = JSON.parse(result[0]).result;
          resultBank = JSON.parse(result[1]).isExist;
          var img = document.getElementById('cardNumberValidity');
          if (!resultProbook) {
            document.getElementById('cardNumberValid').value = -1;
            img.hidden = false;
            img.src = '/public/images/svg/remove-symbol.svg'; 
          } else if (!resultBank) {
            document.getElementById('cardNumberValid').value = -2;
            img.hidden = false;
            img.src = '/public/images/svg/remove-symbol.svg'; 
          } else {
            document.getElementById('cardNumberValid').value = 1;
            img.hidden = false;
            img.src = '/public/images/svg/check-sign.svg';
          }
        })
        .catch((error) => {
          console.log(error);
        });
      }
    }

  </script>
</body>
</html>
