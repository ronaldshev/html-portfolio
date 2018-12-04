var values = [];

function performStatistics(){
  var area = document.getElementById("area"); // gets values in textarea
  values = area.value.split("  "); //splits each number spread with two spaces
  values.sort(function(a,b){return a - b}); // sorts the array numerically
  for(var i = 0; i < values.length; i++) // sets each value in the array to an integer from a string
    values[i] = +values[i];
  document.getElementById("sum").value=calcSum(values);
  document.getElementById("mean").value=calcMean(values);
  document.getElementById("median").value=calcMedian();
  document.getElementById("max").value=findMax();
  document.getElementById("min").value=findMin();
  document.getElementById("mode").value=calcMode();
  document.getElementById("variance").value=calcVariance(document.getElementById("mean").value);
  document.getElementById("stddev").value=calcStdDev(document.getElementById("variance").value);
  return false;
}

function calcMean(arr){
  var sum = calcSum(arr);
  return (sum/arr.length).toFixed(2);
}

function calcMedian(){
  var half = Math.floor(values.length / 2);
  if(values.length % 2)
    return values[half].toFixed(2);
  else {
    return((values[half-1] + values[half])/2.0).toFixed(2);
  }
}

function calcMode(){
  var modeMap = {},
    maxCount = 1,
    modes = [];

  for(var i = 0; i < values.length; i++){
    var el = values[i];
    if(modeMap[el] == null)
      modeMap[el] = 1;
    else
      modeMap[el]++;
    if(modeMap[el] > maxCount){
      modes = [el];
      maxCount = modeMap[el];
    }
    else if(modeMap[el] == maxCount){
      modes.push(el);
      maxCount = modeMap[el];
    }
  }
  var modeString = "";
  for(i = 0; i < modes.length; i++){ // puts all modes from array into a string
    modeString += (modes[i] + " ");  // with one space between all of them for multi
  }
  return modeString;
}

function calcStdDev(variance){
  return Math.sqrt(variance).toFixed(2);
}

function calcSum(arr){
  var sum = 0;
  for(var i = 0; i < arr.length; i++){
    sum += arr[i];
  }
  return sum.toFixed(2);
}

function calcVariance(mean){
  var varArr = [];
  for(var i = 0; i < values.length; i++){
    varArr[i] = values[i] - mean;
    varArr[i] = Math.pow(varArr[i], 2);
  }
  return calcMean(varArr);
}

function findMax(){
  var max = values[0];
  for(var i = 0; i < values.length; i++){
    if(values[i] >= max){
      max = values[i];
    }
  }
  return max.toFixed(2);
}

function findMin(){
  var min = values[0];
  for(var i = 0; i < values.length; i++){
    if(values[i] <= min){
      min = values[i];
    }
  }
  return min.toFixed(2);
}
