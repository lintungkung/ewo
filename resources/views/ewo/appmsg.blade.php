<div class="container bg-grey collapse" id="appMSG" name="divpage">

    <div class="alert alert-primary collapse" role="alert" id="appMSG_alert">
        訊息清單，查詢中
    </div>

</div>

<script>

    $(document).ready(function () {
        //
    });
    /*********** Redy end *************/

    function getPushMSG(){
        var method = "post";
        var path = "/api/getpushmsg";
        var params = {
            p_userCode : $('#p_userCode').val(),
            fromtype : 'app'
        };

        TOP();
        $('#appMSG_alert').collapse('show');

        $.ajax({
            method: method,
            url: path,
            data: params,
            success: function (json) {
                $('#appMSG_alert').collapse('hide');

                if(json.code === "0000")
                {
                    //obj.parents('.card').find('h6').text("約工到府時間:" + (data.data).replace(":00.000",""));
                    $('#appMSG').find('.card').remove();
                    $('#appMSG').find('.alert-warning').remove();

                    var insertHTML = '';
                    var itemNum = 1;
                    json.data.query.forEach(function(t){
                        insertHTML += '' +
                            '<div class="card w-100 mb-3">\n' +
                            '    <div class="card-header list-group-item-info pl-2 pr-2">\n' +
                            '        <label class="m-0">\n' +
                            '            (' + itemNum + ')  '+ t.title + '\n' +
                            '        </label>\n' +
                            '        <label class="float-right m-0">\n' +
                            '            ' + (t.create_at).substr(0,19) + '\n' +
                            '        </label>\n' +
                            '    </div>\n' +
                            '    <div class="card-body p-2">\n' +
                            '        <p>' + t.companyNo + '住編:' + t.custId + ',工單:' + t.workSheet +'</p>\n' +
                            '        <p class="mb-0">' + t.msg +'</p>\n' +
                            '    </div>\n' +
                            '</div>';
                        itemNum = itemNum + 1;
                    });

                    if(insertHTML.length < 1) {
                        insertHTML += '' +
                            '<div class="alert alert-warning" role="alert">\n' +
                            '    沒有訊息;' + json.date
                            '</div>';
                    }

                    $('#appMSG').append(insertHTML)


                }
            }, error(e) {
                $('#appMSG_alert').collapse('hide');
                console.log('getPushMSG=>error');
            }
        });
    }


</script>
