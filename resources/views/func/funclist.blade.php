@extends('func.layouts.default')

@section('title', '九宮格')

@section('content')
    <style>
        .col-4.pos{
            padding: 0 8px;
        }
        .card-deck {
            margin: 2rem .5rem;
        }

        .card-deck .card {
            margin-bottom: 1rem;
        }
        .card-body{
            padding: 1rem;
        }
        .card-text {
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
        }
        .modal-content {
            position: relative;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.2);
            border-radius: 0.3rem;
            outline: 0;
        }
    </style>

{{--    <input type="hidden" id="coImg" value="{{ $p_data['coImg'] }}">--}}
    <input type="hidden" id="timeYMD" value="{{ $p_data['timeYMD'] }}">
    <main style="">
        <div class="container">
            <div class="card-deck">
                <div class="row">
                    @foreach ($list as $k => $t)
                        @switch($t['func'])
                        @case('ewoQAList')
                        <div class="col-4 pos">
                            <div class="card btn btn-wow @if($t['func'] == 'ewoQAList' && $p_data['qaNewItem'] == 'Y') bg-danger @endif" data-toggle="modal" data-target="#loadIng" data-func="{{ $t['func'] }}">
                                <img src="{{ asset($t['img']) }}?i=20230205">
                                <div class="card-body p-0">
                                    <p class="card-text">{{ $t['funChName'] }}</p>
                                </div>
                            </div>
                        </div>
                        @break
                        @default
                            <div class="col-4 pos">
                                <div class="card btn btn-wow" data-toggle="modal" data-target="#loadIng" data-func="{{ $t['func'] }}">
                                    <img src="{{ asset($t['img']) }}?i=20230205">
                                    <div class="card-body p-0">
                                        <p class="card-text">{{ $t['funChName'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @break
                        @endswitch
                    @endforeach
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
        $(document).ready(function() {
            $('div .card .btn').click(function(){
                $('#loadIng').dialog('')
            });


            $('.card').click(function(event){
                var localUrl = document.URL;
                let func = $(this).data('func');
                console.log('func='+func);

                // // 出班檢查
                // if(func == 'ewoOrderList') {
                //     if($('#coImg').val() == 'N') {
                //         alert('請先上傳[設備檢查]照片。');
                //         let today = $('#timeYMD').val();
                //         if(today >= '20221018') {
                //             location.replace("/ewo/func/ewoCheckOut");
                //             return;
                //         }
                //     }
                // }

                if(func == 'ewoDeviceInfo') {
                    app.openUrl('https://www.homeplus.net.tw/file-list-2_2_2.html');
                    return false;
                }

                location.replace("/ewo/func/"+func);

                $('#loadIng').on("hidden.bs.modal", function () {
                    location.href = localUrl;
                    return false;
                });
            })

        }); // end ready




    </script>
@endsection
