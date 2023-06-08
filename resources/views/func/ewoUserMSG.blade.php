@extends('func.layouts.default')

@section('title', '勤務派工APP')

@section('content')

<main>
    <div class="container bg-grey">
        <div class="card w-100 mt-3 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    訊息清單
                </div>
            </div>
            <div class="card-body">
            </div>
        </div>
    </div>

    {{-- LoadIng --}}
    <div class="modal fade" id="loadIng" role="dialog" >
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="showStr">
                            資料查詢中...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

@endsection

@section('script')
<script>

    $(document).ready(function () {
        getUserMsg();
    });
    /*********** Redy end *************/


    function getUserMsg(){
        TOP();

        let data = [];
        data['func'] = 'getUserMsg';
        data['userCode'] = $('#userCode').val();
        data['userName'] = $('#userName').val();
        let dataJson = JSON.stringify(Object.assign({}, data));
        $('.card-body').html('資料查詢中...');
        $('#loadIng').modal('show');

        $.ajax({
            url: '/api/EWOFUNC',
            method: 'post',
            data: dataJson,
            timeout: 10000,
            headers: {"Content-Type": "application/json"},
            success: function (json) {

                if(json.code === "0000") {

                    //obj.parents('.card').find('h6').text("約工到府時間:" + (data.data).replace(":00.000",""));
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

                    $('.card-body').html('')
                    $('.card-body').append(insertHTML)
                    modalClose();
                }

            }, error(e) {
                modalClose();
                console.log('api_error');
                console.log(e);
            }
        });
    }


    function modalClose() {
        for(let i=1; i<10; i++) {
            setTimeout(function(){
                $('#loadIng').modal('hide');
            },1000);
        }
    }


</script>
@endsection
