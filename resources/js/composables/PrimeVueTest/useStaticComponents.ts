import { ref, reactive } from 'vue';

export function useStaticComponents() {
    const staticComponentData = reactive({
        selectOptions: [
            { label: 'Option 1', value: 'opt1' },
            { label: 'Option 2', value: 'opt2' },
            { label: 'Option 3', value: 'opt3' }
        ]
    });

    return {
        staticComponentData
    };
}