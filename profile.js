$(document).ready(function () {

    $(document).on('click', '.profile_photo .edit', function (event) {
        event.stopPropagation();
        $(this).parent('.profile_photo').addClass('editing');
    });

    $(document).on('click', '.text_box .edit', function (event) {
        event.stopPropagation();
        const $field = $(this).parent('.text_box');
        const $fieldValueSpan = $field.find('.valu');
        const $inputField = $field.find('input');

        $field.addClass('editing');
        $inputField.val($fieldValueSpan.text());
        $inputField.focus();
    });

    // save name 
    $('body').on("click", ".savename", function () {
        let parent = $(this).parent();
        let value = parent.find('.valu');
        let inputField = parent.find('input');
        value.text(inputField.val());
        parent.removeClass('editing');
        let name = inputField.val();
        $.ajax({
            url: `profile.php?action=updateName&name=${encodeURIComponent(name)}`,
            type: 'GET',
            context: this,
            success: function () {
                console.log("name updated");
                console.log(name);
            }
        })
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
                        let path = "profile_images/" + response;
                        $('.avtar').attr('src', path);
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
    // $('body').on("click", ".savename", function ()
    $('body').on("change", "#file_upload", function (){
        if (this.files && this.files[0]) {
            let image = $("<img>", {
                id: 'image_preview',
                class: 'only_prev',
                src: 'tp.png'
            }); 
            $('.post_area').append(image);
            let reader = new FileReader();
            reader.readAsDataURL(this.files[0]);
            reader.onload = function (e) {
                $('#image_preview').attr('src', e.target.result);
                console.log("this is the main url of the image stored locally ",e.target.result);
            }
        }
    });

    $('#add_post_card').submit(function (event) {
        event.preventDefault();
        let formData = new FormData(this);
        // console.log(formData);
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
                        let e = document.getElementsByClassName('only_prev');
                        $('.post_area .only_prev').remove();
                    },
                    error: function (error) {
                        console.error("Error:", error);
                        alert("An error occurred during upload.");
                    }
                })
                console.log("call ");
            },
            error: function (error) {
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
        let dislikecount = $(this).find('.fa-thumbs-down').text();
        let likecount = $(this).parent().find('.fa-thumbs-up').text();
        console.log(p_id + " dislike " + dislikecount);
        console.log(p_id + " like " + likecount);
        if( likecount > 0 ){
            likecount--;
        }
        if(dislikecount > 0 ){
            dislikecount = 0 ;
        }
        else{
            dislikecount++;
        }
        console.log("New dislike count " + dislikecount);
        $.ajax({
            url: `profile.php?action=dislike&id=${encodeURIComponent(p_id)}&dislikecount=${encodeURIComponent(dislikecount)}&likecount=${encodeURIComponent(likecount)}`,
            type: 'GET',
            context: this,
            success: function () {
                console.log("after success " + dislikecount);
                console.log("after success " + likecount);
                $(this).find('.fa-thumbs-down').text(dislikecount);
                $(this).parent().find('.fa-thumbs-up').text(likecount);

            }
        })
    });
    $('body').on("click", ".like-button", function (event) {
        event.preventDefault();
        let p_id = $(this).attr('id');
        let likecount = $(this).find('.fa-thumbs-up').text();
        let dislikecount = $(this).parent().find('.fa-thumbs-down').text();
        console.log(p_id + " like " + likecount);
        console.log("New like count " + likecount);
        if( dislikecount > 0 ){
            dislikecount--;
        }
        if(likecount > 0 ){
            likecount = 0 ;
        }
        else{
            likecount++;
        }
        $.ajax({
            url: `profile.php?action=like&id=${encodeURIComponent(p_id)}&dislikecount=${encodeURIComponent(dislikecount)}&likecount=${encodeURIComponent(likecount)}`,
            type: 'GET',
            context: this,
            success: function (response) {
                console.log("after success like " + likecount);
                console.log("after success dislike " + dislikecount);
                $(this).find('.fa-thumbs-up').text(likecount);
                $(this).parent().find('.fa-thumbs-down').text(dislikecount);
            }
        })
    });
    $('.share').click( function(){
        let url = window.location.href ; 
        console.log(url);
    })
});
