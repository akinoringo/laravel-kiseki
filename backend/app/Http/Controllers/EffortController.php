<?php

namespace App\Http\Controllers;

use App\Effort;
use App\Goal;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\EffortRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EffortController extends Controller
{
    //
	public function __construct()
	{
		$this->authorizeResource(Effort::class, 'effort');
	}

	public function index(Request $request) {

		// 検索した語の抽出
		$search = $request->search;		

		$efforts = Effort::orderBy('created_at', 'DESC')
			->where(function($query) use ($search) {
									$query->orwhere('title', 'like', "%{$search}%")
												->orwhere('content', 'like', "%{$search}%");
			})->paginate(10);
			
		return view('home', compact('efforts'));
	}


	public function create(){
		$goals = Goal::where('user_id', Auth::user()->id)
			->where(function($goals){
				$goals->where('status', 0);
			})->get();

		if (!isset($goals[0])) {

			return redirect()->route('mypage.show', ['id' => Auth::user()->id])->with([
				'flash_message' => 'まずは目標を作成してください',
				'color' => 'danger'
			]);
		}

		return view('efforts.create', compact('goals'));
	}


	public function store(EffortRequest $request, Effort $effort ){
		//軌跡の保存処理
		$effort->fill($request->all());
		$effort->goal_id = $request->goal_id;
		$effort->user_id = $request->user()->id;
		$effort->save();

		// 軌跡に紐づく目標と、目標に紐づく軌跡を全て抽出
		$goal = Goal::where('id', $request->goal_id)->get()->first();
		$efforts = Effort::where('goal_id', $request->goal_id)->get();

		// $goalに紐づくeffortsの取り組み時間の合計値($goal->efforts_time)をDBに保存。

		$goal->efforts_time = $this->sumEffortsTime($efforts);
		$goal->save();

		//目標のステータスが0(目標未達成)の場合、goal_time>total(effort_time)であれば目標ステータスを1に更新する。
		if($goal->status === 0) {
			
			$this->updateGoalStatus($goal, $efforts);
			return redirect()->route('mypage.show', ['id' => Auth::user()->id]);			

		} else {

			return redirect()->route('mypage.show',['id' => Auth::user()->id])->with([
				'flash_message' => 'クリア済みの目標です。',
				'color', 'danger'
			]);
		}


	}

	public function edit(Effort $effort){
		// $goals = Goal::all()->sortByDesc('created_at');
		$goals = Goal::where('user_id', Auth::user()->id)
			->where(function($goals){
				$goals->where('status', 0);
			})->get();

		return view('efforts.edit', compact('effort', 'goals'));
	}	

	public function update(EffortRequest $request, Effort $effort){

		$effort->fill($request->all())->save();

		// 軌跡に紐づく目標と、目標に紐づく軌跡を全て抽出
		$goal = Goal::where('id', $effort->goal_id)->get()->first();
		$efforts = Effort::where('goal_id', $effort->goal_id)->get();

		// $goalに紐づくeffortsの取り組み時間の合計値($goal->efforts_time)をDBに保存。
		$goal->efforts_time = $this->sumEffortsTime($efforts);
		$goal->save();		

		//目標のステータスが0(目標未達成)の場合、goal_time>total(effort_time)であれば目標ステータスを1に更新する。
		if($goal->status === 0) {
			
			$this->updateGoalStatus($goal, $efforts);

			return redirect()->route('mypage.show', ['id' => Auth::user()->id])->with([
				'flash_message' => '軌跡を編集しました。',
				'color' => 'success'
			]);			

		} else {

			return redirect()->route('mypage.show', ['id' => Auth::user()->id])->with([
				'flash_message' => 'クリア済みの目標なので、軌跡は編集できません。',
				'color' => 'danger'
			]);
		}

	}	

	public function destroy(Effort $effort)
	{
		// $effortに紐づく$goalの取得
		$goal = Goal::where('id', $effort->goal_id)->get()->first();

		if ($goal->status === 0) {

			// $effortの消去
			$effort->delete();

			// 消去した$effortに紐づいていた$goalに紐づく軌跡合計時間($efforts_time)を再計算
			$efforts = Effort::where('goal_id', $goal->id)->get();
			$goal->efforts_time = $this->sumEffortsTime($efforts);
			$goal->save();
		
			return redirect()->route('mypage.show', ['id' => Auth::user()->id])->with([
				'flash_message' => '軌跡を削除しました。',
				'color' => 'success'
			]);			
		} else {
			return redirect()->route('mypage.show', ['id' => Auth::user()->id])->with([
				'flash_message' => 'クリア済みの目標なので、軌跡は削除できません。',
				'color' => 'danger'
			]);			
		}


	}


	public function show(Effort $effort)
	{
		return view('efforts.show', [
			'effort' => $effort,
			'user' => Auth::user()
		]);
	}	



	// 軌跡の合計時間に応じて目標ステータスを更新する。
	private function updateGoalStatus($goal, $efforts){

		if ($goal->goal_time <= $this->sumEffortsTime($efforts)) {

			$goal->status = 1;
			$goal->save();

			session()->flash('flash_message', 'おめでとうございます。目標をクリアしました。');
			session()->flash('color', 'success');

		} else {

			$goal->status = 0;
			$goal->save();

			session()->flash('flash_message', '軌跡を作成しました。');
			session()->flash('color', 'success');			

		}		

	}

	// 軌跡の合計時間の算出メソッド
	private function sumEffortsTime($efforts) {
		$total_efforts_time = 0;

		foreach ($efforts as $effort) {
			$total_efforts_time += $effort->effort_time;
			
		}
		return $total_efforts_time;
	}	


}
