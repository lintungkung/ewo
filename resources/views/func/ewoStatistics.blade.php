@extends('func.layouts.default')

@section('title', '勤務派工APP')

@section('content')

<main style="">
    <div class="container bg-grey" id="appStatistics" name="divpage">
        <div class="card w-100 mt-3 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    統計清單
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="tabCash">
                    <thead>
                    <tr class="table-success">
                        <th scope="col" class="text-center">公司別</th>
                        <th scope="col" class="text-center">住編</th>
                        <th scope="col" class="text-center">工單</th>
                        <th scope="col" class="text-center">現金</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><th colspan="99">資料查詢中</th></tr>
                    </tbody>
                </table>
                <table class="table table-striped" id="tabSwipe">
                    <thead>
                        <tr class="table-warning">
                            <th scope="col" class="text-center">公司別</th>
                            <th scope="col" class="text-center">住編</th>
                            <th scope="col" class="text-center">工單</th>
                            <th scope="col" class="text-center">刷卡</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><th colspan="99">資料查詢中</th></tr>
                    </tbody>
                </table>

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
            getStatistics();
        });
        /*********** Redy end *************/


        function getStatistics(){
            let data = [];
            data['func'] = 'getStatistics';
            data['userCode'] = $('#userCode').val();
            data['userName'] = $('#userName').val();
            let dataJson = JSON.stringify(Object.assign({}, data));
            $('#loadIng').modal('show');

            $.ajax({
                url: '/api/EWOFUNC',
                method: 'post',
                data: dataJson,
                timeout: 10000,
                headers: {"Content-Type": "application/json"},
                success: function (json) {
                    let htmlStr = '';
                    let htmlStrCash = '';
                    let htmlStrSwipe = '';

                    if(json.code === "0000") {
                        if (json.data.cashList.length < 1) {
                            htmlStrCash = `<tr><th colspan="99">查無資料</th></tr>`;
                        } else {
                            for (const [key, item] of Object.entries(json.data.cashList)) {
                                htmlStrCash += `
                                    <tr>
                                        <th scope="col" class="text-center">${item.CompanyNo}</th>
                                        <th scope="col" class="text-center">${item.CustId}</th>
                                        <th scope="col" class="text-center">${item.WorkSheet}</th>
                                        <th scope="col" class="text-center">$${item.receiveMoney}</th>
                                    </tr>
                                    `;
                            }
                            htmlStrCash += `
                                    <tr>
                                        <th scope="col" class="text-right" colspan="3">現金小計</th>
                                        <th scope="col" class="text-center">$${json.data.cash}</th>
                                    </tr>
                                    `;
                        }
                        $('#tabCash tbody').html('');
                        $('#tabCash tbody').html(htmlStrCash);


                        if (json.data.swipeList.length < 1) {
                            htmlStrSwipe = `<tr><th colspan="99">查無資料</th></tr>`;
                        } else {
                            for (const [key, item] of Object.entries(json.data.swipeList)) {
                                htmlStrSwipe += `
                                    <tr>
                                        <th scope="col" class="text-center">${item.CompanyNo}</th>
                                        <th scope="col" class="text-center">${item.CustId}</th>
                                        <th scope="col" class="text-center">${item.WorkSheet}</th>
                                        <th scope="col" class="text-center">$${item.receiveMoney}</th>
                                    </tr>
                                `;
                            }
                            htmlStrSwipe += `
                                    <tr>
                                        <th scope="col" class="text-right" colspan="3">刷卡小計</th>
                                        <th scope="col" class="text-center">$${json.data.swipe}</th>
                                    </tr>
                                    `;
                        }
                        $('#tabSwipe tbody').html('');
                        $('#tabSwipe tbody').html(htmlStrSwipe);

                    } else {
                        htmlStr = ` <tr><th colspan="99">Error!!!\n Code：${e.code}\n Data：${e.data}</th></tr>`;

                        $('tbody').html('');
                        $('tbody').html(htmlStr);
                    }

                    modalClose();
                }, error(e) {
                    modalClose();
                    console.log('API Error。')
                    console.log(e)
                    $('#loadIng').modal('hide');
                    // let htmlStr = ` <tr><th colspan="99">Error!!!\n Code：${e.code}\n Data：${e.data}</th></tr>`;
                    // $('tbody').html('');
                    // $('tbody').html(htmlStr);;
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
