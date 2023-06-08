@extends('func.layouts.default')

@section('title', '勤務派工APP')

@section('content')

<main style="">
    <div class="container bg-grey">
        <input type="hidden" name="p_token" id="p_token" value="{{ csrf_token() }}">
        <input type="hidden" name="area" id="area" value="{{ $p_data['area'] }}">
        <div class="card w-100 mt-3 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    出班檢查[GO]
                </div>
            </div>
            <div class="card-body p-2">
                <div class="input-group ">
                    <div class="input-group-prepend p-0 col-3">
                        <span class="input-group-text w-100 pl-1">登入帳號</span>
                    </div>
                    <div class="input-group-append p-0 col-9">
                    <span class="input-group-text bg-white w-100">
                        {{ $p_data['userCode'] }}
                    </span>
                    </div>
                </div>
                <div class="input-group ">
                    <div class="input-group-prepend p-0 col-3">
                        <span class="input-group-text w-100 pl-1">名稱</span>
                    </div>
                    <div class="input-group-append p-0 col-9">
                    <span class="input-group-text bg-white w-100">
                        {{ $p_data['userName'] }}
                    </span>
                    </div>
                </div>

{{--                <div class="input-group ">--}}
{{--                    <div class="input-group-prepend p-0 col-3">--}}
{{--                        <span class="input-group-text w-100 pl-1">參考照片</span>--}}
{{--                    </div>--}}
{{--                    <div class="input-group-append p-0 col-9">--}}
{{--                        <img src="{{ asset('img/coImgSampe.jpg') }}" width="100%">--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>

            <div class="card-body p-2">
                <div class="input-group ">
                    <div class="input-group-prepend p-0 col-3">
                        <span class="input-group-text w-100 pl-1">檢查主管：</span>
                    </div>
                    <div class="input-group-append p-0 col-9">
                        <input type="text" class="input-group-text bg-white w-100" id="mangUser" maxlength="20" placeholder="請輸入>>主管[工號 名稱]">
                    </div>
                </div>

                <div class="list-group">
                    <label class="list-group-item bg-info mb-0">
                        &nbsp;&nbsp;<input class="form-check-input me-1" type="checkbox" value="cg" ondblclick="checkBoxAll()">
                        服裝儀容
                    </label>
                    <label class="list-group-item bg-primary mb-0">
                        &nbsp;&nbsp;<input class="form-check-input me-1" type="checkbox" value="vc">
                        車輛整潔
                    </label>
                    <label class="list-group-item bg-success mb-0">
                        &nbsp;&nbsp;<input class="form-check-input me-1" type="checkbox" value="tc">
                        工具清理
                    </label>
                    <label class="list-group-item bg-warning mb-0">
                        &nbsp;&nbsp;<input class="form-check-input me-1" type="checkbox" value="le">
                        勞安設備
                    </label>
                </div>

            </div>
        </div>
{{--        Clothing and grooming cg--}}
{{--        vehicle clean vc--}}
{{--        tool cleaning tc--}}
{{--        Laoan equipment le--}}
        <div class="card w-100 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    <label class="btn btn-info mb-0">
                        <svg width="20" height="20" fill="currentColor" class="bi bi-person-bounding-box" viewBox="0 0 16 16">
                            <path d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1h-3zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5z"></path>
                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path>
                        </svg>
                        <input class="d-none" type="file" accept="image/*" name="cg" onchange="fileUpload($(this))">
                        服裝儀容
                    </label>
                    <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3" id="label_cg">
                        請上傳(服裝儀容)照片。
                    </label>
                </div>
            </div>
            <div class="card-body col-3">
                <img class="" width="500" name="coImg" id="img_cg"
                     src="/ewo_coImg/{{ date('Ym') }}/{{ date('Ymd') }}/coImg_{{ date('Ymd') }}_cg_{{ $p_data['userCode'] }}.jpg?i={{date('His')}}"
                     onerror="$(this).css('display','none')">
            </div>
        </div>
        <div class="card w-100 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    <label class="btn btn-primary mb-0">
                        <svg width="20" height="20" fill="currentColor" class="bi bi-truck" viewBox="0 0 16 16">
                            <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"></path>
                        </svg>
                        <input class="d-none" type="file" accept="image/*" name="vc" onchange="fileUpload($(this))">
                        車輛整潔
                    </label>
                    <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3" id="label_vc">
                        請上傳(車輛整潔)照片。
                    </label>
                </div>
            </div>
            <div class="card-body col-3">
                <img class="" width="500" name="coImg" id="img_vc"
                     src="/ewo_coImg/{{ date('Ym') }}/{{ date('Ymd') }}/coImg_{{ date('Ymd') }}_vc_{{ $p_data['userCode'] }}.jpg?i={{date('His')}}"
                     onerror="$(this).css('display','none')">
            </div>
        </div>
        <div class="card w-100 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    <label class="btn btn-success mb-0">
                        <svg width="20" height="20" fill="currentColor" class="bi bi-wrench-adjustable" viewBox="0 0 16 16">
                            <path d="M16 4.5a4.492 4.492 0 0 1-1.703 3.526L13 5l2.959-1.11c.027.2.041.403.041.61Z"></path>
                            <path d="M11.5 9c.653 0 1.273-.139 1.833-.39L12 5.5 11 3l3.826-1.53A4.5 4.5 0 0 0 7.29 6.092l-6.116 5.096a2.583 2.583 0 1 0 3.638 3.638L9.908 8.71A4.49 4.49 0 0 0 11.5 9Zm-1.292-4.361-.596.893.809-.27a.25.25 0 0 1 .287.377l-.596.893.809-.27.158.475-1.5.5a.25.25 0 0 1-.287-.376l.596-.893-.809.27a.25.25 0 0 1-.287-.377l.596-.893-.809.27-.158-.475 1.5-.5a.25.25 0 0 1 .287.376ZM3 14a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"></path>
                        </svg>
                        <input class="d-none" type="file" accept="image/*" name="tc" onchange="fileUpload($(this))">
                        工具清理
                    </label>
                    <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3" id="label_tc">
                        請上傳(工具清理)照片。
                    </label>
                </div>
            </div>
            <div class="card-body col-3">
                <img class="" width="500" name="coImg" id="img_tc"
                     src="/ewo_coImg/{{ date('Ym') }}/{{ date('Ymd') }}/coImg_{{ date('Ymd') }}_tc_{{ $p_data['userCode'] }}.jpg?i={{date('His')}}"
                     onerror="$(this).css('display','none')">
            </div>
        </div>

        <div class="card w-100 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    <label class="btn btn-warning mb-0">
                        <svg width="20" height="20" fill="currentColor" class="bi bi-cone-striped" viewBox="0 0 16 16">
                            <path d="m9.97 4.88.953 3.811C10.159 8.878 9.14 9 8 9c-1.14 0-2.158-.122-2.923-.309L6.03 4.88C6.635 4.957 7.3 5 8 5s1.365-.043 1.97-.12zm-.245-.978L8.97.88C8.718-.13 7.282-.13 7.03.88L6.275 3.9C6.8 3.965 7.382 4 8 4c.618 0 1.2-.036 1.725-.098zm4.396 8.613a.5.5 0 0 1 .037.96l-6 2a.5.5 0 0 1-.316 0l-6-2a.5.5 0 0 1 .037-.96l2.391-.598.565-2.257c.862.212 1.964.339 3.165.339s2.303-.127 3.165-.339l.565 2.257 2.391.598z"></path>
                        </svg>
                        <input class="d-none" type="file" accept="image/*" name="le" onchange="fileUpload($(this))">
                        勞安設備
                    </label>
                    <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3" id="label_le">
                        請上傳(勞安設備)照片。
                    </label>
                </div>
            </div>
            <div class="card-body col-3">
                <img class="" width="500" name="coImg" id="img_le"
                     src="/ewo_coImg/{{ date('Ym') }}/{{ date('Ymd') }}/coImg_{{ date('Ymd') }}_le_{{ $p_data['userCode'] }}.jpg?i={{date('His')}}"
                     onerror="$(this).css('display','none')">
            </div>
        </div>

        <label class="btn btn-success text-dark mb-6 w-100">
            <input class="d-none" type="button" onclick="chkData()">
            <b>出班去[GO]</b>
        </label>

    </div>
</main>

@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
            //
        getCOImgData();
    }); /*********** Redy end *************/

    function fileUpload(obj) {
        var fname = obj.prop('name')
        var uploadImg = new UploadImg(fname);
        uploadImg.addFileAndSend(obj[0].files[0]);

        function UploadImg(fname) {
            var xhr = new XMLHttpRequest();
            const LENGTH = 1024 * 1024 * 0.3; //0.5M
            var start = 0;
            var end = start + LENGTH;
            var blob;
            var blob_num = 1;
            var is_stop = 0;

            this.addFileAndSend = function (file) {
                // var file = that.files[0];
                blob = cutFile(file);
                sendFile(blob, file, fname);
                blob_num += 1;
            };


            function cutFile(file) {
                var file_blob = file.slice(start, end);
                start = end;
                end = start + LENGTH;
                return file_blob;
            }

            function sendFile(blob, file, fname) {
                var total_blob_num = Math.ceil(file.size / LENGTH);
                var fileName = 'coImg.jpg';
                var columnName = 'coImg';

                var formData = new FormData();
                formData.append("image", blob);
                formData.append("blob_num", blob_num);
                formData.append("total_blob_num", total_blob_num);
                formData.append('_token', '{{csrf_token()}}');
                formData.append('fileName', fileName);
                formData.append('id', $('#p_id').val());
                formData.append('p_userCode', $('#userCode').val());
                formData.append('p_userName', $('#userName').val());
                formData.append('p_CustID', $('#p_custId').val());
                formData.append('p_BookDate', $('#p_BookDate').val());
                formData.append('p_CompanyNo', $('#p_companyNo').val());
                formData.append('p_WorkSheet', $('#p_workSheet').val());
                formData.append('p_columnName', columnName);
                formData.append('p_fname', fname);

                xhr.open("POST", "/ewo/order_info/uploadimg", false);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // console.log(xhr.responseText);
                        var json = JSON.parse(xhr.responseText);
                        console.log(json);
                        $('#label_'+fname).html(json.meg+'#'+json.date);
                        if(json.data !== 'uploading') {
                            if((json.data.src).length > 0) {
                                $('#img_'+fname).attr('src',json.data.src);
                                $('#img_'+fname).css('display','block');
                                $('#label_'+fname).text(json.date+';'+json.meg);
                            }
                        }
                    } //end success

                    var t = setTimeout(function () {
                        if (start < file.size && is_stop === 0) {
                            blob = cutFile(file);
                            sendFile(blob, file, fname);
                            blob_num += 1;
                        } else {
                            setTimeout(t);
                        }
                    }, 1000);
                };
                xhr.send(formData);
            }
        }
    }


    // 全選
    function checkBoxAll() {
        $('input:checkbox').prop('checked', true);
    }


    // 主管確認
    function chkData() {
        let chkLength = $('input:checkbox:checked').length;
        let mangUser = $('#mangUser').val().length;
        let chkImg = 'Y';
        $("img[name='coImg']").each(function(){
            if(chkImg == 'Y' && $(this).css('display') == 'none') {
                chkImg = 'N';
            }
        });
        if(chkLength < 1) {
            alert('請確認檢查項目都有[V]')
            return true;
        }
        if(mangUser < 1) {
            alert('請確認[檢查主管]已經輸入')
            return true;
        }
        if(chkImg == 'N') {
            alert('請確認上傳檔案[照片]都可以看的到!!!')
            return true;
        }
        chkDataPost();
    }


    // 送出
    function chkDataPost() {
        let params = {
            p_id : $('#p_id').val(),
            p_userCode : $('#userCode').val(),
            p_userName : $('#userName').val(),
            p_area : $('#area').val(),
            p_columnName :  'checkOutImgPost',
            EventType :  'checkOutImgPost',
            p_mangUser : $('#mangUser').val(),
            _token : $('#p_token').val(),
        };
        apiEvent(params);
    }


    // ajax
    function apiEvent(params) {
        params['_token'] = $('#p_token').val();
        params['p_userCode'] = $('#userCode').val();
        params['p_userName'] = $('#userName').val();
        console.log('apiEvent==');
        console.log(params);
        $.ajax({
            method: 'POST',
            url: '/ewo/event',
            data: params,
            success: function (json) {
                console.log(json)
                if (json.code === "0000") {
                    alert('送出成功');
                    window.location = '/ewo/func';
                } else {
                    alert('送出異常;'+json.data)
                }
            }
        });
    }


    // 取得，檢查主管
    function getCOImgData(){
        let data = [];
        data['func'] = 'getCOImg';
        data['userCode'] = $('#userCode').val();
        data['userName'] = $('#userName').val();
        let dataJson = JSON.stringify(Object.assign({}, data));
        // $('.card').html('資料查詢中...');

        $.ajax({
            url: '/api/EWOFUNC',
            method: 'post',
            data: dataJson,
            timeout: 10000,
            headers: {"Content-Type": "application/json"},
            success: function (json) {
                console.log(json)
                if(json.code == '0000') {
                    if (typeof json.data.userMang !== 'undefined') {
                        // your code here
                        $('#mangUser').val(json.data.userMang);
                        $('input:checkbox').prop('checked', true);
                    } else {
                        $('#mangUser').val('');
                        $('input:checkbox').prop('checked', false);
                    }
                }

            }, error(e) {
                console.log(e);
            }
        });
    }


</script>
@endsection
