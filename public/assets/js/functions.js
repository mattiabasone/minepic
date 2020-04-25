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
                success: function(response) {
                    $('#search-alert').hide();
                    $('#search-alert .col-md-6').empty();
                    if(response.ok === true) {
                        // Title
                        $('#modal-username-title').html(response.data.username);
                        // Avatar
                        $('#modal-img-avatar').attr('src', SITE_URL+'avatar/256/'+response.data.username);
                        $('#modal-img-avatar').attr('alt', response.data.username+' avatar');
                        $('#modal-img-avatar').attr('title', response.data.username+' avatar');
                        $('#modal-input-avatar').val(SITE_URL+'avatar/'+response.data.username);
                        // Skin
                        $('#modal-img-skin').attr('src', SITE_URL+'skin/256/'+response.data.username);
                        $('#modal-img-skin').attr('alt', response.data.username+' skin');
                        $('#modal-img-skin').attr('title', response.data.username+' skin');
                        $('#modal-input-skin').val(SITE_URL+'skin/'+response.data.username);
                        // Skin Back
                        $('#modal-img-skin-back').attr('src', SITE_URL+'skin-back/256/'+response.data.username);
                        $('#modal-img-skin-back').attr('alt', response.data.username+' skin back');
                        $('#modal-img-skin-back').attr('title', response.data.username+' skin back');
                        $('#modal-input-skin-back').val(SITE_URL+'skin-back/'+response.data.username);
                        // buttons
                        $('#modal-btn-user').attr('href', SITE_URL+'user/'+response.data.username);
                        $('#modal-btn-download').attr('href', SITE_URL+'download/'+response.data.uuid);
                        $('#modal-btn-change').attr('href', 'http://minecraft.net/profile/skin/remote?url='+SITE_URL+'skins/'+response.data.uuid+'.png');
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
        datumTokenizer: function(countries) {
            return Bloodhound.tokenizers.whitespace(countries.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: JSON+'typeahead/%QUERY',
            wildcard: '%QUERY',
            filter: function(response) {
                return response.data;
            }
        }
    });

    $('#user-search').typeahead(null, {
        name: 'usernames',
        source: usernames,
        templates: {
            suggestion: function (data) {
                return '<p class="userinfo"><img src="'+SITE_URL+'avatar/32/'+data.uuid+'" alt="'+data.uuid+'" title="'+data.uuid+'" /> '+data.username+'</p>';
            }
        },
        displayKey: function(usernames) {
            return usernames.uuid;
        },
        limit: Infinity
    });

    $('#user-search').bind('typeahead:close', function(obj) {
        var username = $(obj.currentTarget).val();
        if (username) {
            DisplayUsersInfo(username);
            // This is really bad
            setTimeout(function () {
                $(obj.currentTarget).val("");
            }, 750);
        }
    });

    $('.show-user-info').click(function() {
        DisplayUsersInfo($(this).attr('data-user'));
    });

});