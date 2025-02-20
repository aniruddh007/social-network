$(document).ready(function () {

    $(document).on('click', '.profile_photo .edit', function (event) {
        event.stopPropagation();
        $(this).parent('.profile_photo').addClass('editing');
    });

    // save profile photo
    $("#upload_pic").change(function () {
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            let x = this;
            reader.onload = function (e) {
                $('#profile_preview').attr('src', e.target.result);
                const file = x.files[0];
                const formData = new FormData();
                formData.append('profile_pic', file);
                $.ajax({
                    url: `profile.php?action=updatePic`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        const a = $('.avtar').attr('src');
                        console.log("profile pic uploaded successfully ")
                        console.log(a);
                        console.log("Response is ");
                        console.log(typeof response);
                        console.log(response);
                        let path = "profile_images/"+ response;
                        $('.avtar').attr('src',path);
                        console.log(path);
                    },
                    error: function (error) {
                        console.error("Error:", error);
                        alert("Some error occurred during upload.");
                    }
                });
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
    $("#file_upload").change(function () {
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.readAsDataURL(this.files[0]);
            reader.onload = function (e) {
                $('#image_preview').attr('src', e.target.result);
                console.log(e.target.result);
            }

        }
    });

    $('#add_post_card').submit(function (event) {
        event.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: 'profile.php?action=add_post',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $.ajax({
                    url: 'profile.php?action=get_posts',
                    type: 'GET',
                    success: function (response) {
                        $('.posts').html(response);
                        document.getElementById('add_post_card').reset();
                        let e = document.getElementById('image_preview');
                        e.src = 'tp.png';
                    },
                    error: function (error) {
                        console.error("Error:", error);
                        alert("An error occurred during upload.");
                    }
                })
                console.log("call ");
            },
            error: function (xhr, status, error) {
                alert("An error occured : " + error);
            }
        });
    });
    $('body').on("click", ".close", function (event) {
        event.preventDefault();
        let parentDiv = $(this).parent().parent();
        let idname = parentDiv.attr('id');
        $.ajax({
            url: 'profile.php?action=delete_post&id=' + idname,
            type: 'GET',
            success: function (response) {
                $("#" + idname).remove();
            }
        })

    });
    $('body').on("click", ".dislike-button", function (event) {
        event.preventDefault();
        let p_id = $(this).attr('id');
        let count = $(this).find('.fa-thumbs-down').text();
        console.log(p_id + " dislike " + count);
        count++;
        console.log("New dislike count " + count);
        $.ajax({
            url: `profile.php?action=dislike&id=${encodeURIComponent(p_id)}&count=${encodeURIComponent(count)}`,
            type: 'GET',
            context: this,
            success: function () {
                console.log("after success " + count);
                $(this).find('.fa-thumbs-down').text(count);
            }
        })
    });
    $('body').on("click", ".like-button", function (event) {
        event.preventDefault();
        let p_id = $(this).attr('id');
        let count = $(this).find('.fa-thumbs-up').text();
        console.log(p_id + " like " + count);
        count++;
        console.log("New like count " + count);
        $.ajax({
            url: `profile.php?action=like&id=${encodeURIComponent(p_id)}&count=${encodeURIComponent(count)}`,
            type: 'GET',
            context: this,
            success: function (response) {
                console.log("after success " + count);
                $(this).find('.fa-thumbs-up').text(count);
            }
        })
    });
});
