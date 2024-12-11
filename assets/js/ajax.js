jQuery(document).ready(function ($) {
  function count_second() {
    $(".resend-otp").addClass("deactive");
    let counter = $("#timer-validation").attr("data-time"); // شروع شمارش از 120

    // استفاده از setInterval برای شمارش هر ثانیه
    let interval = setInterval(function () {
      $("#timer-validation").html(counter); // نمایش مقدار فعلی شمارش در کنسول
      counter--; // کاهش مقدار شمارش به یک

      // وقتی شمارش به صفر رسید، توقف می‌کنیم
      if (counter < 0) {
        clearInterval(interval);
        $(".resend-otp").removeClass("deactive");
      }
    }, 1000); // هر 1000 میلی‌ثانیه (1 ثانیه) یک بار اجرا می‌شود
  }
  // ارسال کد تایید و ایجاد درخواست
  //send otp when otp_verified = 0
  $(document).on("click", "#opt-sender", function () {
    var form_steps = $(this).parents(".form-step");
    var national_id = $(form_steps).find("#national_id").val();
    var phone_number = $(form_steps).find("#phone_number").val();
    var steps = $(form_steps).attr("data-step");
    MaskShow();
    // Send OTP via AJAX
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      type: "POST",
      data: {
        action: "otp_sender_action",
        national_id: national_id,
        phone_number: phone_number,
        steps: steps,
        nonce: o2pay_ajax.nonce,
      },
      success: function (response) {
        MaskHide();
        if (response.success) {
          var hidden_request_id = response.data["request_id"];
          var hidden_phone_number = response.data["phone_number"];
          var hidden_national_id = response.data["national_id"];

          var birthdate = response.data["birthdate"];
          var firstname = response.data["firstname"];
          var lastname = response.data["lastname"];
          var national_id_img_url = response.data["national_id_img_url"];

          var zip_code = response.data["zip_code"];
          var province = response.data["province"];
          var district = response.data["district"];
          var city = response.data["city"];
          var street = response.data["street"];
          var floor = response.data["floor"];
          var no = response.data["no"];

          var address_post =
            province + " " + district + " " + city + " " + street;

          var category = response.data["category"];

          $("#hidden_request_id").val(hidden_request_id);
          $("#hidden_phone_number").val(hidden_phone_number);
          $("#hidden_national_id").val(hidden_national_id);
          $("#hidden_province").val(province);
          $("#hidden_district").val(district);
          $("#hidden_street").val(street);
          $("#hidden_city").val(city);
          $("#hidden_cat").val(category);

          $("#first_name").val(firstname);
          $("#last_name").val(lastname);
          $("#birth_date").val(birthdate);

          if (zip_code) {
            $(".postal-step-2").show();
            $("#postalcode").val(zip_code);
            $("#address_post").val(address_post);
            $("#address_post_no").val(no);
            $("#address_post_floor").val(floor);
          }

          $(".type-facilities").each(function () {
            if ($(this).val() == category) {
              $(this).attr("checked");
            }
          });

          $(form_steps).slideUp();
          $(".phone-num").text(hidden_phone_number);
          $(form_steps).next().slideDown();
        } else {
          alert(response.data.message);
        }
      },
      error: function (error) {
        MaskHide();
        alert(error);
      },
    });
  });

  $(document).on("click", "#resend_otp:not(.deactive)", function () {
    var national_id = $("#hidden_national_id").val();
    var phone_number = $("#hidden_phone_number").val();
    MaskShow();
    // Send OTP via AJAX
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      type: "POST",
      data: {
        action: "otp_resend_action",
        phone_number: phone_number,
        national_id: national_id,
        nonce: o2pay_ajax.nonce,
      },
      success: function (response) {
        MaskHide();
        if (response.success) {
        }
      },
      error: function (error) {
        MaskHide();
        alert(error);
      },
    });
  });

  $(".previous-step").click(function (e) {
    e.preventDefault();
    var form_steps = $(this).parents(".form-step");
    $(form_steps).slideUp();
    $(".phone-num").text(hidden_phone_number);
    $(form_steps).prev().slideDown();
  });

  // تایید کد تایید و رفتن به مرحله تکمیل اطلاعات
  $(document).on("click", "#otp-varify", function () {
    // جمع‌آوری مقادیر ورودی‌ها فقط زمانی که روی دکمه کلیک می‌شود
    var values = $(".otp-code-field")
      .map(function () {
        return $(this).val(); // دریافت مقدار هر input
      })
      .get(); // به آرایه تبدیل می‌شود

    // چک کردن که آیا همه ورودی‌ها پر شده‌اند یا نه
    if (values.includes("")) {
      alert("لطفا همه فیلدها را پر کنید");
      return; // اگر یکی از فیلدها خالی بود، فرایند متوقف می‌شود
    }
    var form_steps = $(this).parents(".form-step");
    // ارسال OTP و سایر داده‌ها به سرور
    var otp_code = values.join(""); // مقادیر ورودی‌ها را به هم متصل می‌کنیم تا یک OTP کامل بسازیم
    var request_id = $("#hidden_request_id").val(); // ID درخواست به دلخواه شما
    var steps = $(form_steps).attr("data-step");
    // ارسال درخواست با AJAX
    MaskShow();
    $.ajax({
      url: o2pay_ajax.ajax_url, // آدرس ارسال درخواست
      type: "POST",
      data: {
        action: "verify_otp_action", // اکشن مشخص شده در وردپرس
        request_id: request_id, // ID درخواست
        otp_code: otp_code, // کد OTP که به هم متصل شده است
        steps: steps,
        nonce: o2pay_ajax.nonce, // nonce برای امنیت
      },
      success: function (response) {
        MaskHide();
        if (response.success) {
          $(form_steps).slideUp();
          var phone_number_info = $("#hidden_phone_number").val();
          var national_id_info = $("#hidden_national_id").val();
          $("#phone_number_info").val(phone_number_info);
          $("#national_id_info").val(national_id_info);
          $(form_steps).next().slideDown();
        } else {
          // خطا در تایید OTP
          alert(response.data);
        }
      },
      error: function (error) {
        MaskHide();
        // خطا در ارسال درخواست
        console.error("خطا در ارسال درخواست", error);
      },
    });
  });

  // تکمیل اطلاعات و رفتن به مرحله کدپستی
  $(document).on("click", "#update_request", function () {
    var form_steps = $(this).parents(".form-step");
    var request_id = $("#hidden_request_id").val();
    var national_id = $("#hidden_national_id").val();
    var phone_number = $("#hidden_phone_number").val();
    var firstname = $(form_steps).find("#first_name").val();
    var lastname = $(form_steps).find("#last_name").val();
    var birthdate = $(form_steps).find("#birth_date").val();
    var steps = $(form_steps).attr("data-step");
    var national_id_img_url = $("input#image_national_id")[0].files[0];

    // بررسی اگر فایل انتخاب نشده باشد
    if (!national_id_img_url) {
      alert("لطفا تصویر کارت ملی را انتخاب کنید.");
      return; // جلوگیری از ادامه فرایند اگر فایل وجود ندارد
    }

    // ایجاد FormData برای ارسال داده‌ها و فایل
    var formData = new FormData();
    formData.append("action", "loan_request");
    formData.append("nonce", o2pay_ajax.nonce);
    formData.append("national_id", national_id);
    formData.append("phone_number", phone_number);
    formData.append("firstname", firstname);
    formData.append("lastname", lastname);
    formData.append("birthdate", birthdate);
    formData.append("request_id", request_id);
    formData.append("steps", steps);
    formData.append("national_id_img_url", national_id_img_url); // اضافه کردن فایل

    // ارسال درخواست AJAX
    MaskShow();
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      method: "POST",
      data: formData,
      processData: false, // جلوگیری از پردازش خودکار داده‌ها توسط jQuery
      contentType: false, // جلوگیری از تنظیم contentType توسط jQuery
      success: function (response) {
        MaskHide();
        if (response.success) {
          $(form_steps).slideUp();
          $(form_steps).next().slideDown();
        } else {
          alert(response.data["message"]);
        }
      },
      error: function (error) {
        MaskHide();
        alert("خطا در ارسال درخواست: " + error.responseText);
      },
    });
  });

  // استعلام کدپستی
  $(document).on("click", "#inquiry-postalcode", function () {
    var form_steps = $(this).parents(".form-step");
    var postalcode = $(form_steps).find("#postalcode").val();

    // Perform the AJAX request
    MaskShow();
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      type: "POST",
      data: {
        action: "get_postal_code", // The action hook on the backend
        postalcode: postalcode,

        nonce: o2pay_ajax.nonce, // Ensure this is being passed and is correctly localized
      },
      success: function (response) {
        MaskHide();
        if (response.success) {
          var PostalCodeData = response.data["data"];
          var province = PostalCodeData["province"];
          var district = PostalCodeData["district"];
          var city = PostalCodeData["city"];
          var street = PostalCodeData["street"];
          var no = PostalCodeData["no"];
          var floor = PostalCodeData["floor"];
          var address = province + " " + district + " " + city + " " + street;
          $(form_steps).find("#address_post").val(address);
          $(form_steps).find("#address_post_no").val(no);
          $(form_steps).find("#address_post_floor").val(floor);

          // ذخیره اطلاعات آدرس برای ارسال به دیتابیس
          $("#hidden_province").val(province);
          $("#hidden_district").val(district);
          $("#hidden_city").val(city);
          $("#hidden_street").val(street);
          $("#address_post_no").val(no);
          $("#address_post_floor").val(floor);

          $(form_steps).find(".postal-step-2").slideDown();
        } else {
          // Handle the failure response
          alert(response.data["message"]);
        }
      },
      error: function (xhr, status, error) {
        MaskHide();
        // Handle AJAX errors
        console.error("AJAX error:", status, error);
        // Optionally display a generic error message
        // $('#error-message').html('An error occurred. Please try again.');
      },
    });
  });

  // تایید آدرس بر اساس کد پستی ارسالی
  $(document).on("click", "#submit-postalcode", function () {
    var form_steps = $(this).parents(".form-step");
    var postalcode = $(form_steps).find("#postalcode").val();
    var province = $("#hidden_province").val();
    var district = $("#hidden_district").val();
    var city = $("#hidden_city").val();
    var street = $("#hidden_street").val();
    var floor = $(form_steps).find("#address_post_floor").val();
    var no = $(form_steps).find("#address_post_no").val();
    var Id = $("#hidden_request_id").val();
    var national_id = $("#hidden_national_id").val();
    var steps = $(form_steps).attr("data-step");
    // Perform the AJAX request
    MaskShow();
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      type: "POST",
      data: {
        action: "post_postal_code", // The action hook on the backend
        Id: Id,
        postalcode: postalcode,
        province: province,
        district: district,
        city: city,
        street: street,
        floor: floor,
        no: no,
        steps: steps,
        nonce: o2pay_ajax.nonce, // Ensure this is being passed and is correctly localized
      },
      success: function (response) {
        MaskHide();
        if (response.success) {
          $(form_steps).slideUp();
          $(form_steps).next().slideDown();
        } else {
          // Handle the failure response
          console.error("Error fetching postal code data:", response.data);
          // Optionally show a message to the user, e.g.:
          // $('#error-message').html(response.data);
        }
      },
      error: function (xhr, status, error) {
        MaskHide();
        // Handle AJAX errors
        console.error("AJAX error:", status, error);
        // Optionally display a generic error message
        // $('#error-message').html('An error occurred. Please try again.');
      },
    });
  });

  // انتخاب دسته بندی تسهیلات
  $(document).on("click", ".type-facilities:not(.deactive)", function () {
    var form_steps = $(this).parents(".form-step");
    var selectedRadio = $(form_steps).find(".type-facilities:checked");
    var cat = selectedRadio.val();
    var national_id = $("#hidden_national_id").val();
    var steps = $(form_steps).attr("data-step");
    console.log(cat);
    // Perform the AJAX request
    MaskShow();
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      type: "POST",
      data: {
        action: "post_category_facilities", // The action hook on the backend
        national_id: national_id,
        cat: cat,
        steps: steps,
        nonce: o2pay_ajax.nonce, // Ensure this is being passed and is correctly localized
      },
      success: function (response) {
        MaskHide();
        if (response.success) {
          $("#hidden_cat").val(cat);
          $(form_steps).slideUp();
          if (cat == "gold") {
            $(form_steps).next().slideDown();
          } else if (cat == "appliance") {
            $(form_steps)
              .siblings('[data-cat="appliance"]')
              .first()
              .slideDown();
          }
        } else {
          // Handle the failure response
          console.error("Error fetching postal code data:", response.data);
          // Optionally show a message to the user, e.g.:
          // $('#error-message').html(response.data);
        }
      },
      error: function (xhr, status, error) {
        MaskHide();
        // Handle AJAX errors
        console.error("AJAX error:", status, error);
        // Optionally display a generic error message
        // $('#error-message').html('An error occurred. Please try again.');
      },
    });
  });

  // انتخاب دسته بندی تسهیلات
  $(document).on("click", "#submin_pre_invoice", function () {
    var form_steps = $(this).parents(".form-step");
    var amount = $(form_steps).find("#bank-amount").text();
    var installments = $(form_steps).find(".month-btn.active span").text();
    var national_id = $("#hidden_national_id").val();
    var requests_id = $("#hidden_request_id").val();
    var method = "mehr-bank";
    var steps = "completed";
    // Perform the AJAX request
    MaskShow();
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      type: "POST",
      data: {
        action: "sender_pre_invoice", // The action hook on the backend
        requests_id: requests_id,
        national_id: national_id,
        amount: amount,
        installments: installments,
        method: method,
        steps: steps,
        nonce: o2pay_ajax.nonce, // Ensure this is being passed and is correctly localized
      },
      success: function (response) {
        MaskHide();
        if (response.success) {
          $(form_steps).slideUp();
          $(form_steps).next().slideDown();
        } else {
          console.error("Error fetching postal code data:", response.data);
        }
      },
      error: function (xhr, status, error) {
        MaskHide();
        console.error("AJAX error:", status, error);
      },
    });
  });

  $(document).on("click", "#choose-method-installment", function () {
    var form_steps = $(this).parents(".form-step");
    var national_id = $("#hidden_national_id").val();
    var requests_id = $("#hidden_request_id").val();
    var steps = $(form_steps).attr("data-step");
    var method = $(this).attr("data-method");
    // Perform the AJAX request
    MaskShow();
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      type: "POST",
      data: {
        action: "post_method_installment", // The action hook on the backend
        requests_id: requests_id,
        national_id: national_id,
        method: method,
        steps: steps,
        nonce: o2pay_ajax.nonce, // Ensure this is being passed and is correctly localized
      },
      success: function (response) {
        MaskHide();
        if (response.success) {
          $(form_steps).slideUp();
          $(form_steps).next().slideDown();
        } else {
          console.error("Error fetching postal code data:", response.data);
        }
      },
      error: function (xhr, status, error) {
        MaskHide();
        console.error("AJAX error:", status, error);
      },
    });
  });

  $(document).on("click", "#choose_installments", function () {
    var form_steps = $(this).parents(".form-step");
    var national_id = $("#hidden_national_id").val();
    var requests_id = $("#hidden_request_id").val();
    var steps = $(form_steps).attr("data-step");
    var bank_installment = $(form_steps).find("#bank_installment").text();
    var method_month = $(form_steps)
      .find(".item-choose-method-month.active")
      .text();
    // Perform the AJAX request
    MaskShow();
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      type: "POST",
      data: {
        action: "post_choose_installments", // The action hook on the backend
        requests_id: requests_id,
        national_id: national_id,
        bank_installment: bank_installment,
        method_month: method_month,
        steps: steps,
        nonce: o2pay_ajax.nonce, // Ensure this is being passed and is correctly localized
      },
      success: function (response) {
        MaskHide();
        if (response.success) {
          $(form_steps).slideUp();
          $(form_steps).next().slideDown();
        } else {
          console.error("Error fetching postal code data:", response.data);
        }
      },
      error: function (xhr, status, error) {
        MaskHide();
        console.error("AJAX error:", status, error);
      },
    });
  });

  $(document).on(
    "click",
    "#varify-rules, #varify_rules, #accept_inquiry",
    function () {
      var form_steps = $(this).parents(".form-step");
      $(form_steps).slideUp();
      $(form_steps).next().slideDown();
    }
  );

  $(document).on("click", "#varify_validation", function () {
    var form_steps = $(this).parents(".form-step");
    var phone_number = $("#hidden_phone_number").val();
    var national_id = $("#hidden_national_id").val();
    var request_id = $("#hidden_request_id").val();
    var steps = $(form_steps).attr("data-step");
    MaskShow();
    // Perform the AJAX request
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      type: "POST",
      data: {
        action: "check_varify_validation", // The action hook on the backend
        national_id: national_id,
        phone_number: phone_number,
        request_id: request_id,
        steps: steps,
        nonce: o2pay_ajax.nonce, // Ensure this is being passed and is correctly localized
      },
      success: function (response) {
        MaskHide();
        count_second();
        if (response.success) {
          $(form_steps).slideUp();
          $(form_steps).next().slideDown();
        } else {
          console.error("Error fetching postal code data:", response.data);
        }
      },
      error: function (xhr, status, error) {
        MaskHide();
        console.error("AJAX error:", status, error);
      },
    });
  });

  $(document).on("click", "#varify_check:not(.deactive)", function () {
    var form_steps = $(this).parents(".form-step");
    var request_id = $("#hidden_request_id").val();
    var national_id = $("#hidden_national_id").val();
    var steps = "completed";
    var file_check_front = $("input#file_check_front")[0].files[0];
    var file_check_back = $("input#file_check_back")[0].files[0];
    var file_check_saydi = $("input#file_check_saydi")[0].files[0];
    MaskShow();
    // بررسی اگر فایل انتخاب نشده باشد
    if (!file_check_front) {
      alert("لطفا تصویر روی چک را بارگزاری نمایید");
      return; // جلوگیری از ادامه فرایند اگر فایل وجود ندارد
    }
    // بررسی اگر فایل انتخاب نشده باشد
    if (!file_check_back) {
      alert("لطفا تصویر پشت چک را بارگزاری نمایید");
      return; // جلوگیری از ادامه فرایند اگر فایل وجود ندارد
    }
    // بررسی اگر فایل انتخاب نشده باشد
    if (!file_check_front) {
      alert("لطفا تصویر شناسه صیادی را بارگزاری نمایید");
      return; // جلوگیری از ادامه فرایند اگر فایل وجود ندارد
    }

    // ایجاد FormData برای ارسال داده‌ها و فایل
    var formData = new FormData();
    formData.append("action", "submit_varify_check");
    formData.append("nonce", o2pay_ajax.nonce);
    formData.append("national_id", national_id);
    formData.append("request_id", request_id);
    formData.append("steps", steps);
    formData.append("file_check_front", file_check_front); // اضافه کردن فایل
    formData.append("file_check_back", file_check_back); // اضافه کردن فایل
    formData.append("file_check_saydi", file_check_saydi); // اضافه کردن فایل

    // ارسال درخواست AJAX
    MaskShow();
    // Perform the AJAX request
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      method: "POST",
      data: formData,
      processData: false, // جلوگیری از پردازش خودکار داده‌ها توسط jQuery
      contentType: false, // جلوگیری از تنظیم contentType توسط jQuery
      success: function (response) {
        MaskHide();
        if (response.success) {
          $(form_steps).slideUp();
          $(form_steps).next().slideDown();
        } else {
          alert(response.data["message"]);
        }
      },
      error: function (error) {
        MaskHide();
        alert("خطا در ارسال درخواست: " + error.responseText);
      },
    });
  });

  $(document).on("click", "#varify_inquiry", function () {
    var form_steps = $(this).parents(".form-step");
    var national_id = $("#hidden_national_id").val();
    var request_id = $("#hidden_request_id").val();
    var otp_code = $("#otp_code_validation").val();
    var steps = $(form_steps).attr("data-step");

    // Perform the AJAX request
    MaskShow();
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      type: "POST",
      data: {
        action: "check_otp_varify_validation", // The action hook on the backend
        national_id: national_id,
        otp_code: otp_code,
        request_id: request_id,
        steps: steps,
        nonce: o2pay_ajax.nonce, // Ensure this is being passed and is correctly localized
      },
      success: function (response) {
        MaskHide();
        if (response.success) {
          console.log(response);
          if (response.data <= 500) {
            $reject =
              'رتبه اعتباری شما بررسی شد. متاسفانه پاسخ اعتبار شما <span style="color:#FF2138;">رد شده</span> است. میتوانید با تصحیح سوابق اعتباری خود مجددا تلاش کنید. <br> با <span style="color:#FF2138">لغو فرآیند</span> میتوانید تسهیلات را از ابتدا درخواست دهید.';
            $("#response_validation_title").html($reject);
            $("#image_validations").hide();
            $("#image_reject").show();
          }
          $(form_steps).slideUp();
          $(form_steps).next().slideDown();
        } else {
          console.error("Error fetching postal code data:", response.data);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX error:", status, error);
      },
    });
  });

  $(document).on("click", "#resend_otp_code", function () {
    var form_steps = $(this).parents(".form-step");
    var phone_number = $("#hidden_phone_number").val();
    var national_id = $("#hidden_national_id").val();
    var request_id = $("#hidden_request_id").val();
    var steps = $(form_steps).attr("data-step");

    // Perform the AJAX request
    MaskShow();
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      type: "POST",
      data: {
        action: "resend_otp_code", // The action hook on the backend
        national_id: national_id,
        phone_number: phone_number,
        request_id: request_id,
        steps: steps,
        nonce: o2pay_ajax.nonce, // Ensure this is being passed and is correctly localized
      },
      success: function (response) {
        MaskHide();
        if (response.success) {
          $("#timer-validation").html(response.messages);
          $("#timer-validation").attr("data-time", response.messages);
          count_second();
        } else {
          console.error("Error fetching postal code data:", response.data);
        }
      },
      error: function (xhr, status, error) {
        MaskHide();
        console.error("AJAX error:", status, error);
      },
    });
  });

  $(document).on("click", "#convert_finance_gold", function () {
    // Perform the AJAX request
    MaskShow();
    $type_order = "gold";
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      type: "POST",
      data: {
        action: "convert_finance_gold", // The action hook on the backend
        type: $type_order,
        nonce: o2pay_ajax.nonce, // Ensure this is being passed and is correctly localized
      },
      success: function (response) {
        MaskHide();
        if (response.success) {
          // $(form_steps).slideUp();
          // $(form_steps).next().slideDown();
        } else {
          console.error("Error fetching postal code data:", response.data);
        }
      },
      error: function (xhr, status, error) {
        MaskHide();
        console.error("AJAX error:", status, error);
      },
    });
  });

  $(document).on('click', '#submit_freed_gold', function(){
    let freed_gold = $().val();
    $.ajax({
      url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
      type: "POST",
      data: {
        action: "submit_freed_gold", // The action hook on the backend
        freed_gold: freed_gold,
        nonce: o2pay_ajax.nonce, // Ensure this is being passed and is correctly localized
      },
      success: function (response) {
        MaskHide();
        if (response.success) {
          // $(form_steps).slideUp();
          // $(form_steps).next().slideDown();
        } else {
          console.error("Error fetching postal code data:", response.data);
        }
      },
      error: function (xhr, status, error) {
        MaskHide();
        console.error("AJAX error:", status, error);
      },
    });
  });
  
  // $(document).on('click', '#login_wensite', function(){
  //   var national_id = $("#hidden_national_id").val();
  //   var request_id = $("#hidden_request_id").val();
  //   $.ajax({
  //     url: o2pay_ajax.ajax_url, // وردپرس متغیر ajaxurl را به صورت خودکار تعریف می‌کند
  //     type: "POST",
  //     data: {
  //       action: "login_wensite", // The action hook on the backend
  //       national_id: national_id,
  //       request_id: request_id,
  //       nonce: o2pay_ajax.nonce, // Ensure this is being passed and is correctly localized
  //     },
  //     success: function (response) {
  //       MaskHide();
  //       if (response.success) {
  //         // $(form_steps).slideUp();
  //         // $(form_steps).next().slideDown();
  //       } else {
  //         console.error("Error fetching postal code data:", response.data);
  //       }
  //     },
  //     error: function (xhr, status, error) {
  //       MaskHide();
  //       console.error("AJAX error:", status, error);
  //     },
  //   });
  // });

});

function MaskShow(){
  jQuery("#lottie-container").show();
  jQuery("#mask").addClass('active');
}
function MaskHide(){
  jQuery("#lottie-container").hide();
  jQuery("#mask").removeClass('active');
}