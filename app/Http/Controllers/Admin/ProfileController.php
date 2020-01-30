<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Profile;
use App\Profilehistory;
use Carbon\Carbon;

class ProfileController extends Controller
{
    //
  public function add()
  {
    return view('admin.profile.create');
  }

  public function create(Request $request)
  {
    // Varidationを行う
    $this->validate($request, Profile::$rules);
    
    // モデルのインスタンスを生成する
    $Profile = new Profile;
    // リクエストのパラメーターを連想配列に変換して$formに入れる
    $form = $request->all();
    
    // 不要なパラメーターを削除する
    unset($form['_token']);
    unset($form['image']);
    
    // データベースに保存する
    $Profile->fill($form);
    $Profile->save();

    return redirect('admin/profile/create');
  }

  public function edit(Request $request)
  {
    // リクエストからidを取得し、そのidを持つデータをprofilesテーブルから検索する
    $profile = Profile::find($request->id);
    
    // プロフィール編集画面にデータベースから取ってきたデータをprofileという名前で渡す
    return view('admin.profile.edit', ['profile' => $profile]);
  }

  public function update(Request $request)
  {
        $this->validate($request, Profile::$rules);
        $profile = Profile::find($request->id);
        $profile_form = $request->all();

        unset($profile_form['_token']);
        unset($profile_form['remove']);
        $news->fill($profile_form)->save();

        // 以下を追記
        $profilehistory = Profilehistory;
        $profilehistory->profile_id = $profile->id;
        $profilehistory->edited_at = Carbon::now();
        $profilehistory->save();

        return redirect('admin/profile/');
  }
}