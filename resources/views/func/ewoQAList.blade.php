@extends('func.layouts.default')

@section('title', '勤務派工APP')

@section('content')
<style>
    .bg-ffaec9 { background-color: #ffaec9; }
</style>
<main>
    <div class="container bg-grey">
        <label class="alert alert-success w-100 mt-3 mb-2" id="title">
            QA清單&nbsp;&nbsp;<b class="text-danger" id="newItemCount"></b>
        </label>
        <div class="w-100" id="listDiv">查詢中...</div>

        <div class="card">
            <div class="card-header p-0">
                <button class="btn btn-link text-left pl-2" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Collapsible Group Item #1
                </button>
            </div>
            <div class="card-body p-0 collapse show" id="collapseOne">
                <ul class="list-group">
                    <li class="list-group-item p-0 pl-2">
                        <div class="card-header p-0">
                            <button class="btn btn-link text-left pl-4" type="button" data-toggle="collapse" data-target="#collapseOne2" aria-expanded="true" aria-controls="collapseOne">
                                Collapsible Group Item #1
                            </button>
                        </div>
                        <div class="card-body collapse show p-0 pl-4 pb-2" id="collapseOne2">
                            testcollapseOne2
                        </div>
                    </li>
                    <li class="list-group-item p-0">
                        <div class="card-header p-0">
                            <button class="btn btn-link text-left pl-4" type="button" data-toggle="collapse" data-target="#collapseOne23" aria-expanded="true" aria-controls="collapseOne">
                                Collapsible Group Item #1
                            </button>
                        </div>
                        <div class="card-body collapse show p-0 pl-4 pb-2" id="collapseOne23">
                            collapseOne23
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- LoadIng --}}
    <div class="modal fade" id="loadIngQaMang" role="dialog" >
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
            $('#loadIngQaMang').modal('show');
            getQaList();
        });
        /*********** Redy end *************/

        // 點選紀錄
        function addClickEvent(id) {
            let url = '/api/EWOFUNC';
            let data = [];
            data['func'] = 'addQAClickEvent';
            data['userCode'] = $('#userCode').val();
            data['userName'] = $('#userName').val();
            data['qaId'] = id;
            let dataJson = JSON.stringify(Object.assign({}, data));
            console.log(dataJson)
            console.log(data)
            $.ajax({
                url: url,
                type: 'post',
                headers: {"Content-Type": "application/json"},
                data: dataJson,
                success: function (result) {
                    //
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    //
                }
            });
        }


        function getQaList(){
            TOP();

            let data = [];
            data['func'] = 'getQAList';
            data['userCode'] = $('#userCode').val();
            data['userName'] = $('#userName').val();
            let dataJson = JSON.stringify(Object.assign({}, data));
            $('.card').html('資料查詢中...');

            $.ajax({
                url: '/api/EWOFUNC',
                method: 'post',
                data: dataJson,
                timeout: 10000,
                headers: {"Content-Type": "application/json"},
                success: function (json) {
                    modalHide();
                    if(json.code === "0000") {
                        htmlEdit(json);
                    } else {
                        console.log(json);
                        alert(json.code+'#'+json.data)
                    }

                }, error(e) {
                    modalHide();
                    console.log(e);
                }
            });
        }


        function htmlEdit(json) {
            let list = json.data;
            $('.card').remove();
            $('#listDiv').html('');
            let newItemCount = 0;

            Object.entries(list).forEach(([k, list2]) => {
                Object.entries(list2).forEach(([k2, t2]) => {
                    let htmlStr = ``;
                    let linkStr = ``;
                    let fileStr = ``;
                    if(k === '0') {
                        // 大類
                        htmlStr = `
        <div class="card">
            <div class="card-header p-0">
                <button class="btn btn-link text-left pl-2" type="button" data-toggle="collapse" data-target="#collapseId${t2.Id}">
                    (${t2.code}) ${t2.title}
                </button>
            </div>
            <div class="card-body p-0 collapse show" id="collapseId${t2.Id}">
                 <ul class="list list-group">
                </ul>
            </div>
        </div>
                        `;

                        $('#listDiv').append(htmlStr);

                    } else {
                        // 細分
                        let inherit = t2.inherit;
                        let bgColor = '';
                        let fontColor = '';
                        let linkData = JSON.parse(t2.link);
                        let fileData = JSON.parse(t2.file);
                        let url = "{{config('order.R1_URL')}}";

                        if(linkData != null) {
                            Object.keys(linkData).forEach((key) => {
                                let lTitle = linkData[key]['title'];
                                let lLink = linkData[key]['link'];

                                linkStr += `
        <li class="list-group-item p-0 pl-2" onclick="addClickEvent('${t2.Id}')">
            <span class="btn btn-outline-success" onclick="app.openUrl('${lLink}')">${lTitle}</span>
        </li>
                                `;
                            });
                            if(linkStr.length > 0)
                            linkStr = `
        <ul class="list-group">
            <li class="list-group-item active bg-info p-0">Link清單：</li>
            ${linkStr}
        </ul>
                            `;
                        }

                        if(fileData !== null && fileData.length > 0) {
                            fileData.forEach((file) => {
                                let fileType = file.substr(fileData.length - 5);
                                if(['.jpg','.png','.gif'].includes(fileType.toLowerCase())) {
                                    fileStr += `
        <li class="list-group-item p-0 pl-2" onclick="addClickEvent('${t2.Id}')">
            <img src="${url}/storage/${file}" width="50" onclick="app.openUrl('${url}/storage/${file}')" >
        </li>
                                `;
                                } else {
                                    let fname = file.replace('ewoQaMang/','');
                                    fileStr += `
        <li class="list-group-item p-0 pl-2" onclick="addClickEvent('${t2.Id}')">
            <span class="btn btn-outline-success" onclick="app.openUrl('${url}/storage/${file}')">${fname}</span>
        </li>
                                `;
                                }
                            });

                            if(fileStr.length > 0)
                            fileStr = `
        <ul class="list-group">
            <li class="list-group-item active bg-info p-0">檔案清單：</li>
            ${fileStr}
        </ul>
                            `;
                        }

                        bgColor = (t2.updateItem <= 8)? 'bg-ffaec9' : '';
                        fontColor = (t2.newItem > 0)? 'text-danger' : '';
                        if(t2.newItem > 0) newItemCount += 1;

                        htmlStr = `
        <li class="list-group-item p-0">
            <div class="card-header p-0 ${bgColor}">
                <button class="btn btn-link text-left pl-4 ${fontColor}" type="button" data-toggle="collapse" data-target="#detailId${t2.Id}">
                    (${t2.code}) ${t2.title}
                </button>
            </div>
            <div class="card-body collapse show p-0 pl-4 pb-2" id="detailId${t2.Id}">
                ${t2.bodydesc}
                ${fileStr}
                ${linkStr}
            </div>
        </li>
                        `;
                        $('#collapseId'+inherit+' ul.list').append(htmlStr);

                    }
                });
            });

            if(newItemCount > 0)
                $('#newItemCount').text(`有${newItemCount}筆新增`);

        }

        // 關閉[Loading]
        function modalHide() {
            setTimeout(
                function() {
                    $('#loadIngQaMang').modal('hide');
                }, 1500);
        }
    </script>
@endsection
