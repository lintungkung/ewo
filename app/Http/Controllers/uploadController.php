<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class uploadController extends Controller
{
	public function index(Request $request)
	{
		if ($request->hasFile('avatar')) {
			$file = $request->file('avatar'); //獲取UploadFile例項
			if ($file->isValid()) { //判斷檔案是否有效
				Storage::disk('upload')->putFileAs(
					'test', $request->file('avatar'), 'test.jpg'
				);
			}
		}
        return redirect()->away('/ewo/8code');
	}

}
