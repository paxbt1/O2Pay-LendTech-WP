jQuery(document).ready(function($){

    $('#birth_date').datepicker();
    $('#date-filter').datepicker();
    
    $('.otp-code-field').on('input', function() {
        // بررسی طول مقدار ورودی
        var maxLength = $(this).attr('maxlength'); // مقدار maxlength
        var currentLength = $(this).val().length;  // طول فعلی ورودی
    
        // اگر مقدار ورودی به maxlength رسید
        if (currentLength == maxLength) {
          // پیدا کردن input بعدی
          var nextInput = $(this).next('.otp-code-field');
          
          // اگر input بعدی وجود دارد، focus به آن منتقل می‌شود
          if (nextInput.length) {
            nextInput.val('');
            nextInput.focus();
          }
        }
    });
    $('#postalcode').on('input', function() {
        // بررسی طول مقدار ورودی
        var maxLength = $(this).attr('maxlength'); // مقدار maxlength
        var currentLength = $(this).val().length;  // طول فعلی ورودی
    
        // اگر مقدار ورودی به maxlength رسید
        if (currentLength == maxLength) {
          // پیدا کردن input بعدی
          var nextInput = $('#inquiry-postalcode').removeAttr('disabled');
          
          // اگر input بعدی وجود دارد، focus به آن منتقل می‌شود
          if (nextInput.length) {
            nextInput.val('');
            nextInput.focus();
          }
        }
      });

      $('span#image_national_id').on('click',function () { 
          $('input#image_national_id').click();
      });
      

      
      $('input#image_national_id').on('change', function() {
          var fileName = $(this).val().split('\\').pop();
          $('#national_id_file_name').text(fileName || 'هیچ فایلی انتخاب نشده است');
      });
      
      $('span#file_check_front').on('click',function () {
          $('input#file_check_front').click();
      });

      $('input#file_check_front').on('change', function() {
          var fileName = $(this).val().split('\\').pop();
          $('#file_check_front').text(fileName || 'هیچ فایلی انتخاب نشده است');
      });      
      
      $('span#file_check_back').on('click',function () {
          $('input#file_check_back').click();
      });

      $('input#file_check_back').on('change', function() {
          var fileName = $(this).val().split('\\').pop();
          $('#file_check_back').text(fileName || 'هیچ فایلی انتخاب نشده است');
      });      
      
      $('span#file_check_saydi').on('click',function () {
          $('input#file_check_saydi').click();
      });

      $('input#file_check_saydi').on('change', function() {
          var fileName = $(this).val().split('\\').pop();
          $('#file_check_saydi').text(fileName || 'هیچ فایلی انتخاب نشده است');
      });

      // بررسی تغییرات در فیلدهای فایل
      $('.images_check').on('change', function() {
        var file_check_front = $("input#file_check_front")[0].files[0];
        var file_check_back = $("input#file_check_back")[0].files[0];
        var file_check_saydi = $("input#file_check_saydi")[0].files[0];
        // بررسی اینکه آیا فیلدهای مورد نظر پر شده‌اند
        if (file_check_front && file_check_back && file_check_saydi) {
          // حذف کلاس "deactive" از دکمه تایید
          $('#varify_check').removeClass('deactive');
        } else {
          // در صورت عدم پر شدن فیلدها، اضافه کردن کلاس "deactive"
          $('#varify_check').addClass('deactive');
        }
      });
      // =====================تبدیل طلا به نقدی و نقدی به طلا ====================
      
      // نرخ روز طلا زیر از بک اند پاس داده شوند
  const daily_price = $("#curent_gold_price").attr('data-price'); // The multiplier constant

  // const incrementAmount = 100000; // Amount to increment or decrement price
  // const currencySymbol = 'تومان'; // Symbol for displaying price

  var loan_amount=0;

  // Function to format numbers with commas and currency symbol
  function formatNumber(num) {
    return  num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  // Function to update the gold based on the price
  function updateGoldFromPrice() {
    const price = parseFloat($('#price').val().replace(/[^0-9.-]+/g, ''));
    if (isNaN(price)) return; // Exit if price is invalid
    $('input#gold').val(price / daily_price);
  }
 
  // Event listener for changes to the price input
  $('#price').on('input', function() {
    updateGoldFromPrice();
  });

  // Event listener for changes to the gold input
  $('input#gold').on('input', function() {
    var maxValue = 143365000; // مقدار بیشینه مجاز
    let $val=Number($(this).val());
    var val=Number($(this).val());
    const price = $val * daily_price;
    if(price <= maxValue && $val >= 1){
      $('#price').val(formatNumber(price)); // Update price field
      calculate_table(price);
    } else if($val <= 1) {
      $('#price').val(daily_price); // Update price field
      calculate_table(143365000);
      $(this).val(1);
    }else{
      $('#price').val(formatNumber(143365000)); // Update price field
      calculate_table(143365000);
      $(this).val(maxValue/daily_price);
    }
    
  });

  // Button click handlers for increasing or decreasing the price
  $('.btn-plus').on('click', function() {
    var maxValue = 143365000; // مقدار بیشینه مجاز
    var price = parseFloat($('#price').val().replace(/[^0-9.-]+/g, '')) + parseInt(daily_price);
    if(price <= maxValue){
      $('#price').val(formatNumber(price)); // Update price field
      calculate_table(price);
      updateGoldFromPrice();
    }else{
      $('#price').val(formatNumber(143365000)); // Update price field
      calculate_table(143365000);
      updateGoldFromPrice();
    }

  });

  $('.btn-minus').on('click', function() {
    let price = parseFloat($('#price').val().replace(/[^0-9.-]+/g, '')) - parseInt(daily_price);

    if(price <= daily_price){
      $('#price').val(formatNumber(daily_price)); // Update price field
      calculate_table(daily_price);
      updateGoldFromPrice();
    }else{
      $('#price').val(formatNumber(price)); // Update price field
      calculate_table(price);
      updateGoldFromPrice();
    }
  });
  
  $('#price').on('input',function(){
    price=parseFloat($(this).val());
    calculate_table(price);
    
  });

  $('#slider_method').on('input',function(){
    price=parseFloat($(this).val());
    price= price * 1000000
    calculate_table(price);
    
  });
// ========================== محاسبه گر ===================

function calculate_table(price) {


  const preciseMultiplier = 200000000 / 143664946;
  
  var down_payment = 0;
  if(price>143664946){
    // down_payment = price-143664946;
    price=143664946;
  }else{
     down_payment = 0;
  }
  // Helper function to format numbers with commas and round to the nearest 1000
  function formatNumber(num) {
    // Round to the nearest 1000 and add commas
    let rounded = Math.round(num / 1000) * 1000;
    return rounded.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  // Calculations
  const total = price * parseFloat(1.94);
  // const bank_amount = price * parseFloat(1.39354998);
  const bank_amount = price * preciseMultiplier; 
  const operation_cost = bank_amount * parseFloat(0.21);
  
  const installment_amount = total / 36;
  // Display values with formatted numbers
  $('#total-repayment').text(formatNumber(total));
  $('#bank_installment').text(formatNumber(bank_amount));
  $("#bank-amount").text(formatNumber(bank_amount));
  $("#requested-amount").text(formatNumber(price));
  $("#request_installment").text(formatNumber(price));
  $("#operation-cost").text(formatNumber(operation_cost));
  $("#fee_installment").text(formatNumber(operation_cost));
  $("#installment-amount").text(formatNumber(installment_amount));
  $("#per_installment").text(formatNumber(installment_amount));
  // $("#down-payment").text(formatNumber(down_payment) + " تومان");
  
}

var swiper = new Swiper(".swiper-method", {
  slidesPerView: 1.9,
  centeredSlides: true,
  pagination: {
    el: ".method-pagination",
    clickable: true,
  },
});

    // هنگام تغییر مقدار اسلایدر، مقدار جدید را در span نمایش بده
    $('#slider_method').on('input', function() {
      $('#value-slider-method').text($(this).val());
    });

});
jalaliDatepicker.startWatch();



function increaseQuantity(productId) {
  // Find the product in the selectedProducts array
  const product = selectedProducts.find(item => item.id === productId);
  
  if (product) {
    // Get the product's quantity input field and decrease button by ID
    const quantityInput = document.getElementById(`quantity-${productId}`);
    const decreaseButton = document.getElementById(`decreas-${productId}`);

    // Check the current quantity and adjust the decrease button icon accordingly
    if (product.quantity > 0) {
      // If quantity is more than 1, set decrease button to "minus" symbol
      decreaseButton.innerHTML = "-";
    } 

    // Increment the product quantity
    product.quantity += 1;

    // Update the quantity input field in the UI
    quantityInput.value = product.quantity;

    // Refresh the displayed list of selected products (cart UI)
    updateSelectedProductsList();
  }
}


function decreaseQuantity(productId) {

  const product = selectedProducts.find(item => item.id === productId);
  if (product && product.quantity > 1) {
    product.quantity -= 1;
    updateSelectedProductsList();
    if(product.quantity == 1){
      const decreaseButton = document.getElementById(`decreas-${productId}`);
      decreaseButton.innerHTML = "&#128465;";
    }
  } else {
    // Remove product if quantity reaches zero
    removeFromCart(productId);
  }
}



// ============================ سبد محضولات ============================

let selectedProducts = [];

function addToCart(productId, productName, productPrice) {
  // Find the product in the selectedProducts array
  const existingProduct = selectedProducts.find(product => product.id === productId);

  if (existingProduct) {
    // If product exists, increase its quantity
    existingProduct.quantity += 1;
  } else {
    // Add new product with initial quantity of 1
    selectedProducts.push({ id: productId, name: productName, price: productPrice, quantity: 1 });

    // Hide add-to-cart button and show quantity selector for the new product
    const productCard = document.getElementById(`product-${productId}`);
    const addToCartButton = productCard.querySelector('.add-to-cart');
    const quantitySelector = productCard.querySelector('.quantity-selector');
    
    addToCartButton.style.display = 'none';
    quantitySelector.style.display = 'flex';
  }

  // Update the selected products list in the UI
  updateSelectedProductsList();
}





function removeFromCart(productId) {
  // Remove the product from selectedProducts
  selectedProducts = selectedProducts.filter(product => product.id !== productId);
  
  // Update UI by calling updateSelectedProductsList
  updateSelectedProductsList();

  // Reset the add-to-cart button for the removed product
  const productCard = document.getElementById(`product-${productId}`);
  if (productCard) {
    productCard.querySelector('.add-to-cart').style.display = 'block';
    productCard.querySelector('.quantity-selector').style.display = 'none';
    const quantityInput = productCard.querySelector('.product-quantity');
    if (quantityInput) {
      quantityInput.value = 1; // Reset quantity input to 1
    }
  }
}

function updateSelectedProductsList() {
  const selectedProductsList = document.getElementById("selected-products-list");
  selectedProductsList.innerHTML = ""; // Clear the list

  // Loop through selected products and add them to the UI
  selectedProducts.forEach(product => {
    const productRow = document.createElement("div");
    productRow.classList.add("product-row");

    const productDetails = document.createElement("div");
    productDetails.classList.add("product-details");

    const productTitle = document.createElement("span");
    productTitle.classList.add("product-title");
    productTitle.innerText = `${product.name}`;

    const productPrice = document.createElement("span");
    productPrice.classList.add("product-price");
    productPrice.innerText = `${product.price * product.quantity}`;

    const deleteButton = document.createElement("button");
    deleteButton.classList.add("delete-button");
    deleteButton.innerHTML = "&#128465;";
    deleteButton.onclick = () => removeFromCart(product.id);

    productDetails.appendChild(productTitle);
    productDetails.appendChild(productPrice);
    productRow.appendChild(productDetails);
    productRow.appendChild(deleteButton);

    selectedProductsList.appendChild(productRow);

    // Update the quantity display in the existing quantity selector
    const quantityInput = document.getElementById(`quantity-${product.id}`);
    if (quantityInput) {
      quantityInput.value = product.quantity;
    }
  });
}


function submitRequest() {
  // Perform AJAX call or other processing
  alert("Submitting the request...");
}

jQuery(document).ready(function($) {
  // فیلد ورودی با id "myInput"
  $('#price').on('input', function() {
    var maxValue = 143365000; // مقدار بیشینه مجاز

    // دریافت مقدار وارد شده
    var inputValue = $(this).val();

    // بررسی اینکه مقدار وارد شده بیشتر از 143664946 است یا خیر
    if (parseInt(inputValue) > maxValue) {
      $(this).val(maxValue); // اگر بیشتر از این مقدار است، آن را به 143664946 تنظیم کن
    }
  });

  

  const animation = lottie.loadAnimation({
    container: document.getElementById('lottie-container'), // Element to render in
    renderer: 'svg', // Render type: 'svg', 'canvas', or 'html'
    loop: true,      // Whether to loop the animation
    autoplay: true,  // Whether to play automatically
    path: assets.assets+'js/loading.json' // Path to your JSON animation file
  });
});
jQuery(document).ready(function($) {
  var initialTime = parseInt($('#resend_otp').attr('data-time'));  // زمان اولیه
  var time = initialTime;  // متغیر زمان که مقدار آن تغییر می‌کند
  var timer;  // متغیر تایمر برای توقف شمارش

  function updateTimer() {
      if (time > 0) {
          time--;  // کاهش زمان هر ثانیه
          $('#counter_resend_otp').text(time);  // نمایش زمان باقی‌مانده
      } else {
          $('#counter_resend_otp').text('ارسال مجدد کد');  // زمانی که شمارش به صفر رسید
          $('#resend_otp').removeClass('deactive');  // فعال کردن دکمه
          clearInterval(timer);  // متوقف کردن شمارش
      }
  }

  // شروع شمارش معکوس
  timer = setInterval(updateTimer, 1000);  // هر 1 ثانیه یکبار

  $(document).on('click', '#resend_otp:not(.deactive)', function(){
    // زمانی که روی دکمه کلیک می‌شود، شمارش معکوس دوباره شروع می‌شود
    time = initialTime;  // بازنشانی زمان به مقدار اولیه
    $('#resend_otp').addClass('deactive');  // غیرفعال کردن دکمه
    $('#resend_otp').text(time);  // نمایش زمان جدید

    // توقف تایمر قبلی و شروع تایمر جدید
    clearInterval(timer);
    timer = setInterval(updateTimer, 1000);
  });

  $('#postalcode').on('input', function() {
    var postalCode = $(this).val(); // مقدار فیلد
    console.log(postalCode);
    if (postalCode.length === 10) {
      $("#inquiry-postalcode").removeClass('deactive'); // حذف کلاس 'deactive' اگر طول 10 باشد
    }
  });

        // وقتی یک گزینه رادیویی انتخاب می‌شود
      $('input[name="choose-recive"]').change(function() {
          checkInputValue();
      });

      // زمانی که مقدار در فیلد وارد می‌شود
      $('#request_freed_gold').on('input', function() {

        var value = Number($(this).val()); // تبدیل مقدار وارد شده به عدد
        var maxValue = Number($(this).attr('max')); // تبدیل مقدار 'max' به عدد
    
        // بررسی اینکه آیا مقدار وارد شده بیشتر از max است
        if (value > maxValue) {
            $(this).val(maxValue); // تنظیم مقدار ورودی به max اگر بیشتر از آن باشد
        }

          checkInputValue();
      });

      // تابع بررسی مقدار ورودی
      function checkInputValue() {
          var inputVal = $('#request_freed_gold').val();
          var resive_method = $('input[name="choose-recive"]:checked').val();
          $('#current_cart_gold').html(inputVal);
          if (inputVal && inputVal > 0 && resive_method) {
              $('#popup_freed_gold').removeClass('deactive');
          } else {
              $('#popup_freed_gold').addClass('deactive');
          }
      }
      $(document).on('click', '#popup_freed_gold:not(.deactive)', function(){
        var content_popup = $('.content-inner-popup').html();
        $('#popup .inner-popup').html(content_popup);
        $('#mask').addClass('active');
        $('#popup').addClass('show');
        
      });
      $(document).on('click', '#close', function(){
        $('#popup').removeClass('show');
        $('#mask').removeClass('active');
      });
});

