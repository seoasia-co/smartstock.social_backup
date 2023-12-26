{{Form::open(["#",'method'=>'GET','class'=>'form-search'])}}
<div class="filter_div">
    <div class="row">
        @foreach($WebmasterSection->customFields as $customField)
            @if($customField->in_search)
                <?php
                if ($customField->$cf_title_var != "") {
                    $cf_title = $customField->$cf_title_var;
                } else {
                    $cf_title = $customField->$cf_title_var2;
                }

                // check field language status
                $cf_land_identifier = "";
                $cf_land_active = false;
                $cf_land_dir = @Helper::currentLanguage()->direction;
                if ($customField->lang_code != "all") {
                    $ct_language = @Helper::LangFromCode($customField->lang_code);
                    $cf_land_identifier = @Helper::languageName($ct_language);
                    $cf_land_dir = $ct_language->direction;
                    if ($ct_language->box_status) {
                        $cf_land_active = true;
                    }
                }
                if ($customField->lang_code == "all") {
                    $cf_land_active = true;
                }
                // required Status
                $cf_required = "";
                ?>
                @if($cf_land_active)
                    @if($customField->type ==12)

                    @elseif($customField->type ==11)

                    @elseif($customField->type ==10)

                    @elseif($customField->type ==9)

                    @elseif($customField->type ==8)
                    @elseif($customField->type ==14)
                        <div class="col-sm-3 m-b">
                            <select name="{{'customField_'.$customField->id}}"
                                    id="{{'customField_'.$customField->id}}"
                                    class="form-control btn-theme c-select" {{$cf_required}}>
                                <option value="">- - {!!  $cf_title !!} - -</option>
                                <option value="1">{{ __('backend.yes') }}</option>
                                <option value="0">{{ __('backend.no') }}</option>
                            </select>
                        </div>
                    @elseif($customField->type ==7 || $customField->type ==6 || $customField->type ==13)
                        <div class="col-sm-3 m-b">
                            <select name="{{'customField_'.$customField->id}}"
                                    id="{{'customField_'.$customField->id}}"
                                    class="form-control btn-theme c-select" {{$cf_required}}>
                                <option value="">{!!  $cf_title !!}</option>
                                <?php
                                $cf_details_var = "details_" . @Helper::currentLanguage()->code;
                                $cf_details_var2 = "details_" . env('DEFAULT_LANGUAGE');
                                if ($customField->$cf_details_var != "") {
                                    $cf_details = $customField->$cf_details_var;
                                } else {
                                    $cf_details = $customField->$cf_details_var2;
                                }
                                $cf_details_lines = preg_split('/\r\n|[\r\n]/', $cf_details);
                                $line_num = 1;
                                ?>
                                @foreach ($cf_details_lines as $cf_details_line)
                                    <option
                                        value="{{ $line_num  }}" {{ (@$_GET['customField_'.$customField->id] == $line_num) ? "selected='selected'":""  }}>{{ $cf_details_line }}</option>
                                    <?php
                                    $line_num++;
                                    ?>
                                @endforeach
                            </select>
                        </div>
                    @elseif($customField->type ==5 || $customField->type ==4)
                        <div class="col-sm-3 m-b">
                            <div class="form-group m-b-0">
                                <div class='input-group date' ui-jp="datetimepicker"
                                     ui-options="{
                format: '{{ Helper::jsDateFormat() }}',
                icons: {
                  time: 'fa fa-clock-o',
                  date: 'fa fa-calendar',
                  up: 'fa fa-chevron-up',
                  down: 'fa fa-chevron-down',
                  previous: 'fa fa-chevron-left',
                  next: 'fa fa-chevron-right',
                  today: 'fa fa-screenshot',
                  clear: 'fa fa-trash',
                  close: 'fa fa-remove'
                },
            allowInputToggle: true,
              }">
                                    {!! Form::text('customField_'.$customField->id,(@$_GET['customField_'.$customField->id]!="")?Helper::formatDate(@$_GET['customField_'.$customField->id]):"", array('placeholder' => $cf_title,'class' => 'form-control btn-theme','id'=>'customField_'.$customField->id,$cf_required=>'', 'dir'=>$cf_land_dir)) !!}
                                    <span class="input-group-addon">
                  <span class="fa fa-calendar"></span>
              </span>
                                </div>
                            </div>
                        </div>
                    @elseif($customField->type ==3)
                        <div class="col-sm-3 m-b">
                            {!! Form::email('customField_'.$customField->id,@$_GET['customField_'.$customField->id], array('placeholder' => $cf_title,'class' => 'form-control btn-theme','id'=>'customField_'.$customField->id,$cf_required=>'', 'dir'=>$cf_land_dir)) !!}
                        </div>
                    @elseif($customField->type ==2)
                        <div class="col-sm-3 m-b">
                            {!! Form::number('customField_'.$customField->id,@$_GET['customField_'.$customField->id], array('placeholder' => $cf_title,'class' => 'form-control btn-theme','id'=>'customField_'.$customField->id,$cf_required=>'','min'=>0, 'dir'=>$cf_land_dir)) !!}
                        </div>
                    @else
                        <div class="col-sm-3 m-b">
                            {!! Form::text('customField_'.$customField->id,@$_GET['customField_'.$customField->id], array('placeholder' => $cf_title,'class' => 'form-control btn-theme','id'=>'customField_'.$customField->id,$cf_required=>'', 'dir'=>$cf_land_dir)) !!}
                        </div>
                    @endif
                @endif
            @endif
        @endforeach
        <div class="col-sm-3 m-b">
            <button class="btn btn-theme" style="width: 100%" id="search-btn" type="submit"><i
                    class="fa fa-search"></i> {{ __('backend.search') }}
            </button>
        </div>
    </div>
</div>
{{Form::close()}}
