import { computed } from 'vue';

export function useKnobTest(widgets: any[]) {
    const knobWidgets = computed(() => 
        widgets.filter(w => w.type === 'knob')
    );

    return {
        knobWidgets
    };
}