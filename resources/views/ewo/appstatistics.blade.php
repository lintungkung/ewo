<div class="container bg-grey collapse" id="appStatistics" name="divpage">

    <div class="alert alert-primary collapse" role="alert" id="appStatistics_alert">
        統計清單，查詢中
    </div>

</div>

<script>

    $(document).ready(function () {
        //
    });
    /*********** Redy end *************/

    function getStatistics(){
        var method = "post";
        var path = "/api/getstatistics";
        var params = {
            p_userCode : $('#p_userCode').val(),
        };

        TOP();
        $('#appStatistics_alert').collapse('show');

        $.ajax({
            method: method,
            url: path,
            data: params,
            success: function (json) {
                $('#appStatistics_alert').collapse('hide');

                if(json.code === "0000")
                {
                    //obj.parents('.card').find('h6').text("約工到府時間:" + (data.data).replace(":00.000",""));
                    $('#appStatistics').find('.card').remove()

                    console.log(json.data.cashList);

                    var insertHTML = '';
                    insertHTML += '' +
                        '<div class="card mb-3">\n' +
                        '    <div class="card-header h4">\n' +
                        '        <label class="mb-0 bg-info">完工_現金</label>\n' +
                        '    </div>\n' +
                        '    <div class="card-body m-0 p-0">\n' +
                        '        <ul class="list-group">\n';

                    json.data.cashList.forEach(function(t){
                        insertHTML += '' +
                            '<li class="list-group-item d-flex justify-content-between align-items-center">\n' +
                            '    <span class="float-left">'+ t.CompanyNo +'-'+ t.WorkSheet +'</span>\n' +
                            '    <span class="float-right">$'+ t.receiveMoney +'</span>\n' +
                            '</li>\n';
                    });
                    insertHTML += '' +
                        '            <li class="list-group-item d-flex justify-content-between align-items-center bg-info">\n' +
                        '                <span class="float-left">現金合計</span>\n' +
                        '                <span class="float-right">$'+ json.data.cash +'</span>\n' +
                        '            </li>\n' +
                        '        </ul>\n' +
                        '    </div>\n' +
                        '</div>\n' +
                        '<div class="card mb-3">\n' +
                        '    <div class="card-header h4">\n' +
                        '        <label class="mb-0 bg-warning">完工_刷卡</label>\n' +
                        '    </div>\n' +
                        '    <div class="card-body m-0 p-0">\n' +
                        '        <ul class="list-group">\n';
                    json.data.swipeList.forEach(function(t){
                        insertHTML += '' +
                            '<li class="list-group-item d-flex justify-content-between align-items-center">\n' +
                            '    <span class="float-left">'+ t.CompanyNo +'-'+ t.WorkSheet +'</span>\n' +
                            '    <span class="float-right">$'+ t.receiveMoney +'</span>\n' +
                            '</li>\n';
                    });
                    insertHTML += '' +
                        '            <li class="list-group-item d-flex justify-content-between align-items-center bg-warning">\n' +
                        '                <span class="float-left">刷卡合計</span>\n' +
                        '                <span class="float-right">$'+ json.data.swipe +'</span>\n' +
                        '            </li>\n' +
                        '        </ul>\n' +
                        '    </div>\n' +
                        '</div>\n' +
                        '查詢時間:' + json.date + '，統計區間:' + json.data.timestart + '~' + json.data.timeend + ';';


                    $('#appStatistics').append(insertHTML)
                }

            }, error(e) {
                $('#appStatistics_alert').collapse('hide');
                console.log('getPushMSG=>error');
            }
        });
    }


</script>
