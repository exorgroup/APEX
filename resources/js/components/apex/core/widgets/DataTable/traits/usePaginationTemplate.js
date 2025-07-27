// resources/js/components/apex/core/widgets/DataTable/traits/usePaginationTemplate.js

import { computed } from 'vue';

export function usePaginationTemplate(props) {
    const paginationTemplateConfig = computed(() => ({
        currentPageReportTemplate: props.currentPageReportTemplate ?? 'Showing {first} to {last} of {totalRecords} entries'
    }));

    return {
        paginationTemplateConfig
    };
}