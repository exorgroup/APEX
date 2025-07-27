import { computed } from 'vue';

export function useDatePickerTest(widgets: any[]) {
    const datePickerWidgets = computed(() => 
        widgets.filter(w => w.type === 'datepicker')
    );

    return {
        datePickerWidgets
    };
}