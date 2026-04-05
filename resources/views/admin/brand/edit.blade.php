@extends('layouts.load')

@section('content')

            <div class="content-area">

              <div class="add-product-content1">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="product-description">
                      <div class="body-area">
                        @include('alerts.admin.form-error')
                      <form id="geniusformdata" action="{{route('admin-brand-update',$data->id)}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Name') }} *</h4>
                                <p class="sub-heading">{{ __('(In Any Language)') }}</p>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <input type="text" class="input-field" name="name" placeholder="{{ __('Enter Name') }}" required="" value="{{ $data->name }}">
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Slug') }} *</h4>
                                <p class="sub-heading">{{ __('(In English)') }}</p>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <input type="text" class="input-field" name="slug" placeholder="{{ __('Enter Slug') }}" required="" value="{{ $data->slug }}">
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                              <h4 class="heading">{{ __('Featured') }}</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <label class="switch">
                              <input type="checkbox" name="is_featured" value="1" {{ (int) $data->is_featured === 1 ? 'checked' : '' }}>
                              <span class="slider round"></span>
                            </label>
                          </div>
                        </div>

												<div class="row">
													<div class="col-lg-4">
														<div class="left-area">
																<h4 class="heading">{{ __('Current logo (image)') }}</h4>
														</div>
													</div>
                          <div class="col-lg-7">
                            <div class="img-upload">
                              <div id="image-preview" class="img-preview" style="background: url({{ $data->image ? asset('assets/images/brands/'.$data->image):asset('assets/images/noimage.png') }});">
                                <label for="image-upload" class="img-label"><i class="icofont-upload-alt"></i>{{ __('Upload Image') }}</label>
                                <input type="file" name="image" class="img-upload">
                              </div>
                            </div>
                          </div>
												</div>

                        <div class="row mt-3">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Secondary photo') }}</h4>
                                <p class="sub-heading">{{ __('Optional') }}</p>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <div class="img-upload">
                              <div id="photo-preview" class="img-preview" style="background: url({{ $data->photo ? asset('assets/images/brands/'.$data->photo):asset('assets/images/noimage.png') }});">
                                <label class="img-label"><i class="icofont-upload-alt"></i>{{ __('Upload') }}</label>
                                <input type="file" name="photo" class="img-upload">
                              </div>
                            </div>
                          </div>
                        </div>

                        <br>
                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <button class="addProductSubmit-btn" type="submit">{{ __('Save') }}</button>
                          </div>
                        </div>
                      </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

@endsection
