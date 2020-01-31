<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Profile;
use App\Profilehistory;
use Carbon\Carbon;

class ProfileController extends Controller
{
  public function index(Request $request)
  {
    $cond_name = $request->cond_name;
    if ($cond_name != '') {
     // 検索されたら検索結果を取得する
     $posts = Profile::where('name', $cond_name)->get();
     } else {
     // それ以外はすべてのニュースを取得する
      $posts = Profile::all();
      
      return view('admin.profile.index', ['posts' => $posts, 'cond_name' => $cond_name]);
     }
  }
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
    return view('admin.profile.edit', ['profile_form' => $profile]);
  }

  public function update(Request $request)
  {
        $this->validate($request, Profile::$rules);
        $profile = Profile::find($request->id);
        $profile_form = $request->all();

        unset($profile_form['_token']);
        unset($profile_form['remove']);
        $profile->fill($profile_form)->save();

        // 以下を追記
        $profilehistory = new Profilehistory;
        $profilehistory->profile_id = $profile->id;
        $profilehistory->edited_at = Carbon::now();
        $profilehistory->save();

        return redirect('admin/profile/');
  }
   // 以下を追記　　
  public function delete(Request $request)
  {
      // 該当するprofile Modelを取得
      $profile = Profile::find($request->id);
      // 削除する
      $profile->delete();
      return redirect('admin/profile/');
  } 
}