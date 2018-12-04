var cardOptions = document.getElementById("paymentControls").innerHTML;
var payPalOptions = 'Email Address: <br><input type="text" id="emailPayPal" name="payPal email"><br>' +
   'Password: <br><input type="password" id="password" name="password"><br>';
var payPalUsed = false;

function testLength(value, length, exactLength){
  if(exactLength == false){
    if(value.length >= length)
      return true;
  }
  else if(exactLength == true){
    if(value.length == length)
      return true;
  }
  return false;
}

function testNumber(value){
  if(!(isNaN(value))){
    return true;
  }
  else{
    return false;
  }
}

function updateForm(control){
    if(control.id === "paypal"){
      document.getElementById("errors").innerHTML = "";
      document.getElementById("paymentControls").innerHTML = payPalOptions;
      payPalUsed = true;
    }
    else if(control.id === "credit"){
      document.getElementById("errors").innerHTML = "";
      document.getElementById("paymentControls").innerHTML = cardOptions;
      payPalUsed = false;
    }
}

function validateControl(control, name, length){
  var correctLength;

  if(testNumber(control) == false){
    document.getElementById("errors").innerHTML = name + ": " +  control + " is not a number.";
    return false;
  }
  else{
    if(name == "Zip"){
      correctLength = testLength(control, length, true);
      if(correctLength == false){
        document.getElementById("errors").innerHTML = name + " must be " + length + " digits.";
        return false;
      }
    }
    else if(name == "CVV2/CVC"){
      correctLength = testLength(control, length, true);
      if(correctLength == false){
        document.getElementById("errors").innerHTML = name + " must be " + length + " digits.";
        return false;
      }
    }
  }
  return true;
}

function validateCreditCard(value){
  var cardType;
  var reqLength;
  var correctLength;
  value = value.replace(/ /g, "");

  if(testNumber(value) == false){
    document.getElementById("errors").innerHTML = "Card Number: " + value + " is not a number.";
    return false;
  }
  else{
    if(value.charAt(0) == 3){
      cardType = "AmEx";
      reqLength = 15;
      correctLength = testLength(value, reqLength, true);
      if(correctLength == false){
        document.getElementById("errors").innerHTML = cardType + " requires " + reqLength + " digits.";
        return correctLength;
      }
      return correctLength;
    }
    else if(value.charAt(0) == 4){
      cardType = "Visa";
      reqLength = 16;
      correctLength = testLength(value, reqLength, true);
      if(correctLength == false){
        document.getElementById("errors").innerHTML = cardType + " requires " + reqLength + " digits.";
        return correctLength;
      }
      return correctLength;
    }
    else if(value.charAt(0) == 5){
      cardType = "Mastercard";
      reqLength = 16;
      correctLength = testLength(value, reqLength, true);
      if(correctLength == false){
        document.getElementById("errors").innerHTML = cardType + " requires " + reqLength + " digits.";
        return correctLength;
      }
      return correctLength;
    }
    else if(value.charAt(0) == 6){
      cardType = "Discover";
      reqLength = 16;
      correctLength = testLength(value, reqLength, true);
      if(correctLength == false){
        document.getElementById("errors").innerHTML = cardType + " requires " + reqLength + " digits.";
        return correctLength;
      }
      return correctLength;
    }
    else{
      document.getElementById("errors").innerHTML = "Invalid credit card type."
      return false;
    }
  }
}

function validateDate(value){
  value = value.substr(5);
  value = parseInt(value, 10);
  var d = new Date();
  var currentDate = d.getMonth() + 1;
  if(value >= currentDate + 1){
    return true;
  }
  else{
    return false;
  }
}

function validateEmail(value){
  var check = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  var gucci = check.test(String(value).toLowerCase());
  if(gucci == false){
    document.getElementById("errors").innerHTML = value + " is an invalid email.";
  }
  return gucci;
}

function validateForm(){
  document.getElementById("errors").innerHTML = "";
  var allGood = false;

  if(payPalUsed == false){
    var address = document.getElementById("address").value;
    var cardNumber = document.getElementById("cardNumber").value;
    var city = document.getElementById("city").value;
    var cvv = document.getElementById("cvv").value;
    var emailCard = document.getElementById("emailCard").value;
    var expiration = document.getElementById("expiration").value;
    var lastName = document.getElementById("lastName").value;
    var nameOnCard = document.getElementById("nameOnCard").value;
    var zip = document.getElementById("zip").value;
    allGood = validateDate(expiration);
    if(allGood == true){
      allGood = validateEmail(emailCard);
      if(allGood == true){
        allGood = validateState();
        if(allGood == true){
          allGood = validateCreditCard(cardNumber);
          if(allGood == true){
            allGood = validateControl(zip, "Zip", 5);
            if(allGood == true){
              allGood = validateControl(cvv, "CVV2/CVC", 3);
              if(allGood == true){
                document.getElementById("errors").innerHTML = "Payment submitted."
              }
            }
          }
        }
      }
    }
  }
  else if(payPalUsed == true){
    var emailPayPal = document.getElementById("emailPayPal").value;
    var password = document.getElementById("password").value;
    allGood = validateEmail(emailPayPal);
    if(allGood == true)
      allGood = validatePassword(password, 3);
      if(allGood == true){
        document.getElementById("errors").innerHTML = "Payment submitted."
      }
    }
  return false;
}

function validatePassword(value, minLength){
  correctLength = testLength(value, minLength, false);
  if(correctLength == false){
    document.getElementById("errors").innerHTML = "Password must be at least " + minLength + " characters.";
    return correctLength;
  }
  return correctLength;
}

function validateState(){
  var state = document.getElementById("state").value;
  if(state == "Select State"){
    document.getElementById("errors").innerHTML = "You must select a state.";
    return false;
  }
  else{
    return true;
  }
}
