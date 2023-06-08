@extends('func.layouts.default')

@section('title', '勤務派工APP')

@section('content')

<main style="">

    <div class="container bg-grey">
        <div class="card w-100 mt-3 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    設備準備清單
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center">工單</th>
                        <th scope="col" class="text-center">施工項目 # 建議設備</th>
                    </tr>
                    </thead>
                    <tbody>
{{--                    @foreach($p_data['orderList'] as $k => $t)--}}
{{--                        @if(!in_array(($t['SheetStatus']),['4.結款','4 結案','A 取消']))--}}
{{--                            @foreach($t['planDevice'] as $k2 => $t2)--}}
{{--                                @if($k2 !== 'null')--}}
{{--                                    <tr>--}}
{{--                                        <th scope="row">{{ $k }}</th>--}}
{{--                                        <td>{{ $k2 }}</td>--}}
{{--                                        <td>{{ $t2 }}</td>--}}
{{--                                    </tr>--}}
{{--                                @endif--}}
{{--                            @endforeach--}}
{{--                        @endif--}}
{{--                    @endforeach--}}
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
<script type="text/javascript">

    $(document).ready(function () {
        getPlanDeviceList();

    });
    /*********** Redy end *************/

    //修改密碼
    function getPlanDeviceList() {
        let data = [];
        data['func'] = 'getPlanDeviceList';
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
                modalClose();
                let htmlStr = '查無資料;'+json.date;

                if(json.code === '0000') {
                    if(Object.keys(json.data).length) {

                        for (const [key, item] of Object.entries(json.data)) {
                            // console.log(`${key}: ${value}`);
                            htmlStr += `
                            <tr>
                                <th class="text-center p-0">${key}</th>
                                <td class="p-0">`;

                            for (const [key2, item2] of Object.entries(item.planDevice)) {
                                htmlStr += `
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item p-0">
                                                    <div class="input-group">
                                                        <div class="col-6 p-0">${key2}</div>
                                                        <div class="col-6 p-0">#${item2}</div>
                                                    </div>
                                                </li>
                                            </ul>
                                            `;
                            }
                            htmlStr += `
                                </td>
                            </tr>
                            `;
                        }

                    }
                }
                $('tbody').html('');
                $('tbody').html(htmlStr);


            }, error: function (data) {
                console.log(data);
                modalClose();
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
