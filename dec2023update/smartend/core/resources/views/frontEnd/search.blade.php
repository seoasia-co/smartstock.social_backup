@extends('frontEnd.layout')

@section('content')

    <section id="inner-headline">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li><a href="{{ route("Home") }}"><i class="fa fa-home"></i></a><i class="icon-angle-right"></i>
                        </li>
                        @if(@$WebmasterSection!="none")
                            <?php
                            $cf_title_var = "title_" . @Helper::currentLanguage()->code;
                            $cf_title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                            $title_var = "title_" . @Helper::currentLanguage()->code;
                            $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                            if (@$WebmasterSection->$title_var != "") {
                                $WebmasterSectionTitle = @$WebmasterSection->$title_var;
                            } else {
                                $WebmasterSectionTitle = @$WebmasterSection->$title_var2;
                            }
                            ?>
                            <li class="active">{!! $WebmasterSectionTitle !!}</li>
                        @elseif(@$search_word!="")
                            <li class="active">{{ @$search_word }}</li>
                        @else
                            <li class="active">{{ $User->name }}</li>
                        @endif
                        @if($CurrentCategory!="none")
                            @if(!empty($CurrentCategory))
                                <?php
                                $category_title_var = "title_" . @Helper::currentLanguage()->code;
                                ?>
                                <li class="active"><i
                                        class="icon-angle-right"></i>{{ $CurrentCategory->$category_title_var }}
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section id="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="search-box">
                        <?php
                        $cf_title_var = "title_" . @Helper::currentLanguage()->code;
                        $field_title = "";
                        $field_id = 0;
                        foreach ($WebmasterSection->customFields->whereIn("type", [0, 2]) as $customField) {
                            if (!empty($customField)) {
                                $field_title = $customField->$cf_title_var;
                                $field_id = $customField->id;
                                break;
                            }
                        }
                        ?>
                        @if($field_id >0)
                            <h3>{{ __('backend.type') }} {{ $field_title }} {{ __('backend.toSearch') }}</h3>
                            {{Form::open(["#",'method'=>'GET','class'=>'form-search'])}}
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" autocomplete="off" placeholder="" name="q">
                                <div class="input-group-btn">
                                    <button class="btn btn-default" type="submit"><i
                                            class="fa fa-search"></i> {{ __('backend.search') }}</button>
                                </div>
                            </div>
                            {{Form::close()}}
                            @if(\request()->input('q') !="")
                                    <div class="alert alert-danger" style="padding: 10px;margin-top: 10px"><strong>{{ __('backend.noResults') }}</strong></div>
                            @endif
                        @else
                            <h3 class="text-center">{{ __('backend.error') }}</h3>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </section>
    <script>

    </script>
@endsection
