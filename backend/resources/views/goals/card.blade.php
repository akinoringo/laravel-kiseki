<div class="card mt-3">
  <div class="card-body d-flex flex-row">
    @if(!empty($user->image))
    <img src="/storage/images/{{$user->image}}" class="rounded-circle mr-2" style="object-fit: cover; width: 50px; height: 50px;">
    @else
    <img src="/images/prof.png" class="rounded-circle mr-2" style="object-fit: cover; width: 50px; height: 50px;">
    @endif
    <div>
      <div class="font-weight-bold"><a class="text-dark" href="{{route('mypage.show', ['id' => $goal->user->id ])}}">{{$goal->user->name}}</a></div>
      <div class="font-weight-lighter">{{ $goal->created_at->format('Y/m/d H:i') }}</div>
    </div>

  @if( Auth::id() === $goal->user_id && $goal->status === 0)
    <!-- dropdown -->
      <div class="ml-auto card-text">
        <div class="dropdown">
          <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            ▼
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route("goals.edit", ['goal' => $goal]) }}">
              <i class="fas fa-pen mr-1"></i>目標を編集する
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" data-toggle="modal" data-target="#modal-delete-{{ $goal->id }}">
              <i class="fas fa-trash-alt mr-1"></i>目標を削除する
            </a>
          </div>
        </div>
      </div>
    <!-- dropdown -->

      <!-- modal -->
      <div id="modal-delete-{{ $goal->id }}" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form method="POST" action="{{ route('goals.destroy', ['goal' => $goal]) }}">
              @csrf
              @method('DELETE')
              <div class="modal-body">
                {{ $goal->title }}を削除します。よろしいですか？
              </div>
              <div class="modal-footer justify-content-between">
                <a class="btn btn-outline-grey" data-dismiss="modal">キャンセル</a>
                <button type="submit" class="btn btn-danger">削除する</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!-- modal -->
  @endif

  </div>
  <div class="card-body pt-0">
    <h3 class="h4 card-title">
      <a class="text-dark" href="{{ route('goals.show', ['goal' => $goal]) }}">
        {{ $goal->title }}
      </a>
    </h3>
    <div class="card-text mb-3">
      <div>内容：</div>
      {{ $goal->content }}
    </div>
    <div class="card-text mt-1">
      <span class="mr-2">継続時間/目標時間：</span>
      {{ $goal->efforts_time }}/{{ $goal->goal_time}}時間
    </div>    

  </div>
</div>