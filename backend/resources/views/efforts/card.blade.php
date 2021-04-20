<div class="card mt-3">
  <div class="card-body d-flex flex-row">
    @if(!empty($effort->user->image))
    <img src="/storage/images/{{$effort->user->image}}" class="rounded-circle mr-2" style="object-fit: cover; width: 50px; height: 50px;">
    @else
    <img src="/images/prof.png" class="rounded-circle mr-2" style="object-fit: cover; width: 50px; height: 50px;">
    @endif     

    <div>
      <div class="font-weight-bold"><a class="text-dark" href="{{route('mypage.show', ['id' => $effort->user->id ])}}">{{$effort->user->name}}</a></div>
      <div class="font-weight-lighter">{{ $effort->created_at->format('Y/m/d H:i') }}</div>
    </div>

  @if( Auth::id() === $effort->user_id && $effort->goal->status === 0)
    <!-- dropdown -->
      <div class="ml-auto card-text">
        <div class="dropdown">
          <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            ▼
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route("efforts.edit", ['effort' => $effort]) }}">
              <i class="fas fa-pen mr-1"></i>軌跡を編集する
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item text-danger" data-toggle="modal" data-target="#modal-delete-{{ $effort->id }}">
              <i class="fas fa-trash-alt mr-1"></i>軌跡を削除する
            </a>
          </div>
        </div>
      </div>
    <!-- dropdown -->

    <!-- modal -->
      <div id="modal-delete-{{ $effort->id }}" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form method="POST" action="{{ route('efforts.destroy', ['effort' => $effort]) }}">
              @csrf
              @method('DELETE')
              <div class="modal-body">
                {{ $effort->title }}を削除します。よろしいですか？
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
    <div class="card-text mb-3">
      目標： {{ $effort->goal->title }}
    </div>    
    <h3 class="h4 card-title">
      <a class="text-dark" href="{{ route('efforts.show', ['effort' => $effort]) }}">
        {{ $effort->title }}
      </a>
    </h3>
    
    <div class="card-text mb-3">
      <div>内容：</div>
      {{ $effort->content }}
    </div>
    <div class="card-text mt-2">
      <span class="mr-1">継続時間：</span>
      {{ $effort->effort_time }}時間
    </div>    
  </div>
</div>