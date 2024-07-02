<form action="/user/edit/rejected/submit" method="post" role="form" enctype="multipart/form-data">
    @csrf
    @method('POST')
    <div class="modal-header">
        <div class="">
            <button class="close waves-effect waves-light btn btn-danger btn-sm pull-right" data-dismiss="modal">
                &times;
            </button>
        </div>
    </div>
    <div class="modal-body">
      <div class="row col-md-12">
        <div class="col-md-12 mt-3">
          <div class="form-group">
              <label class="form-label">Comment</label>
              <textarea id="commenttxt" name="comment" class="mt-2" rows="10" cols="80">
              </textarea>
              <span class="text-danger">@error('comment')
                {{ $message }}
              @enderror</span>
          </div> 
          <input type="text" id="id" name="id" value="{{ $data['id'] }}" hidden>  
          <input type="text" id="type" name="type" value="{{ $data['type'] }}" hidden>  
        </div>
      </div>
    </div>
    <div class="modal-footer">
        <div class="form-group pull-right">
            <input type="submit" name="addtopic" class="form-controlwaves-effect waves-light btn btn-primary btn-sm pull-right" value="submit">
        </div>
    </div>
    </form>

