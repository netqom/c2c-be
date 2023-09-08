<div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Title</label>
														<select name="title" id="title" class="form-control col-md-12" onchange="checkPageExist(this)"  required>
                                                        <option value="">Select</option>
                                                            @php $pages = config('const.pages'); @endphp
															@foreach($pages as $key=>$page)
																<option value="{{ $page }}" >{{ $page }}</option>
															@endforeach
														</select>
													</div>
												</div>