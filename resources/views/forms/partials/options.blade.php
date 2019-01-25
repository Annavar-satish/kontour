@foreach($options as $option_value => $option_display)
  @if($optgroup = is_iterable($option_display) ? $option_value : false)
  <optgroup label="{{ $optgroup }}">
  @endif
  @foreach($optgroup ? $option_display : [$option_value => $option_display] as $option_value => $option_display)
  <option
    value="{{ $option_value }}"
    @if(is_array($selected) ? in_array(strval($option_value), $selected) : $selected == strval($option_value))
      selected
    @endif
  >{{ $option_display }}</option>
  @endforeach
  @if($optgroup)
  </optgroup>
  @endif
@endforeach