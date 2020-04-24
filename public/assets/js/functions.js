$(document).ready(function() {
    var SITE_URL = window.location.origin+'/';
    var JSON = SITE_URL+"api/v1/";

    $('body').tooltip({
        selector: '[rel=tooltip]'
    });

    function ShowAlert(txt, type) {
        $('#search-alert .col-md-6').empty();
        $('#search-alert .col-md-6').append('<div class="alert alert-'+type+'">'+txt+'</div>');
        $('#search-alert').show();
    }

    function DisplayUsersInfo(username) {
        if (username !== '') {
            $.ajax({
                type: 'GET',
                url: JSON+'user/'+username,
                success: function(data) {
                    $('#search-alert').hide();
                    $('#search-alert .col-md-6').empty();
                    if(data.ok == true) {
                        // Title
                        $('#modal-username-title').html(data.userdata.username);
                        // Avatar
                        $('#modal-img-avatar').attr('src', SITE_URL+'avatar/256/'+data.userdata.username);
                        $('#modal-img-avatar').attr('alt', data.userdata.username+' avatar');
                        $('#modal-img-avatar').attr('title', data.userdata.username+' avatar');
                        $('#modal-input-avatar').val(SITE_URL+'avatar/'+data.userdata.username);
                        // Skin
                        $('#modal-img-skin').attr('src', SITE_URL+'skin/256/'+data.userdata.username);
                        $('#modal-img-skin').attr('alt', data.userdata.username+' skin');
                        $('#modal-img-skin').attr('title', data.userdata.username+' skin');
                        $('#modal-input-skin').val(SITE_URL+'skin/'+data.userdata.username);
                        // Skin Back
                        $('#modal-img-skin-back').attr('src', SITE_URL+'skin-back/256/'+data.userdata.username);
                        $('#modal-img-skin-back').attr('alt', data.userdata.username+' skin back');
                        $('#modal-img-skin-back').attr('title', data.userdata.username+' skin back');
                        $('#modal-input-skin-back').val(SITE_URL+'skin-back/'+data.userdata.username);
                        // buttons
                        $('#modal-btn-user').attr('href', SITE_URL+'user/'+data.userdata.username);
                        $('#modal-btn-download').attr('href', SITE_URL+'download/'+data.userdata.uuid);
                        $('#modal-btn-change').attr('href', 'http://minecraft.net/profile/skin/remote?url='+SITE_URL+'skins/'+data.userdata.uuid+'.png');
                        $('#user-info-modal').modal('show');
                    } else {
                        ShowAlert('User not premium or Mojang API request limit per username reached!', 'danger');
                    }
                },
                error: function() {
                    ShowAlert('User not found!', 'danger');
                }
            });
        } else {
            ShowAlert('Username cannot be empty!', 'danger');
        }
    }

    var usernames = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: JSON+'typeahead/%QUERY',
            wildcard: '%QUERY'
        }
    });

    $('#user-search').typeahead(null, {
        name: 'usernames',
        source: usernames,
        templates: {
            suggestion: function (data) {
                return '<p class="userinfo"><img src="'+SITE_URL+'avatar/32/'+data.value+'" alt="'+data.value+'" title="'+data.value+'" /> '+data.label+'</p>';
            }
        },
        limit: Infinity
    }).on('typeahead:selected', function(e, datum) {
        e.preventDefault();
        DisplayUsersInfo(datum.value);
        return false;
    });

    $('#user-search-butt').click(function() {
        var username = $('#user-search').val();
        if (username !== '') {
            DisplayUsersInfo(username);
        } else {
            ShowAlert('Username cannot be empty!', 'danger');
        }
    });

    $('.show-user-info').click(function() {
        DisplayUsersInfo($(this).attr('data-user'));
    });

});