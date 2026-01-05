    <div class="px-3 mt-4">
            <div class="custom-select-1">
            	<select wire:model.debounce.500ms="tahun" class="form-select form-control h-auto py-2">
            		@foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
            	</select>
            </div>
        
    </div>
