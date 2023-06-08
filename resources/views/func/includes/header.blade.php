<style>
    .input-group-append.mr-1 {
        display: inline-block;
    }
    header{
        line-height: 3;
        height: 40px;
    }
    header .container {
        height: 50px;
        position: fixed;
        top: 0;
        z-index: 99;
        background-color: #fff;
        left: 50%;
        transform: translateX(-50%);
    }
    .wordRed { color:red; font-weight:bold; }
    .wordBlue { color:blue; font-weight:bold; }
</style>
<header>
    <input type="hidden" name="userCode" id="userCode" value="{{ $p_data['userCode'] }}">
    <input type="hidden" name="userName" id="userName" value="{{ $p_data['userName'] }}">
    <div class="container">
        <div class="input-group">
            <div>
            <img src="{{ asset('img/logo_01.png') }}" style="width: 200px">
            </div>
        @if($_SERVER['REQUEST_URI'] !== '/ewo/func')
            <div class="input-group-append">
                <button class="btn btn-warning mr-1" id="btnBack" onclick="location.href='/ewo/func';">
                    <svg width="16" height="16" fill="currentColor" class="bi bi-chevron-double-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                        <path fill-rule="evenodd" d="M12.354 1.646a.5.5 0 0 1 0 .708L6.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                    </svg>
                </button>
            </div>

            <div class="input-group-append">
                <button class="btn btn-info" id="btnReload" onclick="location.reload();">
                    <svg width="24" height="24" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                    </svg>
                </button>
            </div>

        @endif
        </div>

    </div>
</header>
