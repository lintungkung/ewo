@extends('func.layouts.default')

@section('title', '勤務派工APP')

@section('content')

<main style="">
    <div class="container bg-grey">
        <div class="card w-100 mt-3 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    設備清單
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center">型號</th>
                        <th scope="col" class="text-center">序號</th>
                    </tr>
                    </thead>
                    <tbody>

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


    {{--        設備序號 qr code        --}}
    <div class="modal fade bd-example-modal-sm" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form class="p-3" id="form_modal">
                    <div class="form-group">
                        <label for="exampleFormControlInput1">公司別</label>
                        <input type="text" class="form-control bg-white" name="companyno" readonly>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">型號</label>
                        <input type="text" class="form-control bg-white" name="CSModel" readonly>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">料號</label>
                        <input type="text" class="form-control bg-white" name="mtno" readonly>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">序號</label>
                        <input type="text" class="form-control bg-white" name="single" readonly>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">QR Code</label>
                        <div id="qrcode"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>

@endsection

@section('script')
<script type="text/javascript" src="{{ asset('js/jquery.qrcode.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        getDeviceList();

        // $('input[name="qrcode_singlesn"]').click(function(){
        //     let companyno = $(this).data('companyno');
        //     let single = $(this).val();
        //     let CSModel = $(this).data('csmodel');
        //     let mtno = $(this).data('mtno');
        //     let qrcode = companyno+','+single+','+mtno+','+CSModel;
        //     console.log(companyno)
        //     console.log(single)
        //     console.log(CSModel)
        //     console.log(mtno)
        //     console.log(qrcode)
        //     $('#form_modal input[name="companyno"]').val(companyno);
        //     $('#form_modal input[name="single"]').val(single);
        //     $('#form_modal input[name="CSModel"]').val(CSModel);
        //     $('#form_modal input[name="mtno"]').val(mtno);
        //     $('#qrcode canvas').remove();
        //     $('#qrcode').qrcode({width: 128,height: 128,text: qrcode});
        // });

    });
    /*********** Redy end *************/

    //設備清單
    function getDeviceList() {
        let data = [];
        data['func'] = 'getDeviceList';
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
                if(json.code == '0000') {
                    editHtml(json.data)
                } else {
                    $('.card-body').html('Code:'+json.code+';Error:'+json.data+'#'+json.date);
                }
                modalClose();
            }, error: function (data) {
                modalClose();
                console.log(data);
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


    // 編輯HTML
    function editHtml(josn) {
        $('#loadIng').modal('hide');
        let list = josn.list;
        let type = josn.type;
        let companyNoList = josn.companyNoList;
        let companyNoStrAry = josn.companyNoStrAry;
        let htmlStr = '';

        for (const [k, t] of Object.entries(companyNoList)) {

            for (const [k2, t2] of Object.entries(type)) {
                htmlStr += `
                            <div class="card mb-3">
                                <div class="card-header pt-0 pb-0" id="list_${t}_${k2}_header">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" data-toggle="collapse" data-target="#list_${t}_${k2}_body">
                                        ${t} | ${companyNoStrAry[t]} (${type[k2]})設備清單
                                    </button>
                                </h5>
                            </div>
                        <div id="list_${t}_${k2}_body" class="collapse show" data-parent="#list_${t}_${k2}_header">
                            <div class="card-body p-0">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="pt-0">
                                                <div class="alert alert-light ml-1" role="alert">型號</div>
                                            </th>
                                            <th scope="col" class="pt-0 m-0">
                                                <label class="alert alert-info pt-1 pb-1">序號</label>
                                                `;
                if (k2 == 'recycle')
                    htmlStr += `<label class="alert alert-warning ml-1 pt-1 pb-1">回收時間</label>`;
                else
                    htmlStr += `<label class="alert alert-info ml-1 pt-1 pb-1">調撥時間</label>`;

                    htmlStr += `
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    `;

                for (const [k3, t3] of Object.entries(list[t][k2])) {
                    htmlStr += `
                                <tr>
                                    <td class="pt-0 pb-0">
                                        <input type="checkbox">${t3.CSModel}
                                    </td>
                                    <td class="pt-0">
                                `;
                    if(k2 == 'recycle') {
                        htmlStr += `
                                <label class="alert alert-info pl-0 pr-0 ml-1 pt-1 pb-1">${t3.SingleSN}</label>
                            `;
                    } else {
                        htmlStr += `
                                <label class="btn btn-info pl-0 pr-0">
                                    <input type="button" class="btn btn-info d-none" onclick="deviceOpenQRCode('id_${t3.SingleSN}')"
                                    data-tes="${k3}" id="id_${t3.SingleSN}"
                                    data-toggle="modal" data-target="#exampleModalCenterxx" value="${t3.SingleSN}"
                                    data-companyno="${t}" data-mtno="${t3.MTNo}" data-csmodel="${t3.CSModel}" >
                                    ${t3.SingleSN}
                                </label>
                            `;
                    }

                    if(k2 == 'recycle')
                        htmlStr += `<label class="alert alert-warning ml-1 pl-0 pr-0">${(t3.BackTime).substring(0,19)}</label>`;
                    else
                        htmlStr += `<label class="alert alert-info ml-1 pl-0 pr-0">${(t3.CreateTime).substring(0,19)}</label>`;

                    htmlStr += `
                            </td>
                        </tr>
                        `;
                }

                htmlStr += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        $('.card-body').html('');
        $('.card-body').html(htmlStr);
    }


    function deviceOpenQRCode(id) {
        $('#exampleModalCenter').modal('show');
        console.log(id)
        let obj = $('#'+id)
        let companyno = obj.data('companyno');
        let single = obj.val();
        let CSModel = obj.data('csmodel');
        let mtno = obj.data('mtno');
        let qrcode = companyno+','+single+','+mtno+','+CSModel;
        console.log(companyno)
        console.log(single)
        console.log(CSModel)
        console.log(mtno)
        console.log(qrcode)
        $('#form_modal input[name="companyno"]').val(companyno);
        $('#form_modal input[name="single"]').val(single);
        $('#form_modal input[name="CSModel"]').val(CSModel);
        $('#form_modal input[name="mtno"]').val(mtno);
        $('#qrcode canvas').remove();
        $('#qrcode').qrcode({width: 128,height: 128,text: qrcode});
    }

</script>
@endsection
