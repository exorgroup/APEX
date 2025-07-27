// resources/js/components/apex/pro/widgets/DataTable/traits/useConditionalStyle.js

import { computed } from 'vue';

export function useConditionalStyle(props) {
    const conditionalStyleConfig = computed(() => ({
        conditionalStyles: props.conditionalStyles ?? []
    }));

    const evaluateCondition = (rowData, style) => {
        const columnValue = rowData[style.column];
        const testValue = style.value;
        const operator = style.operator || 'eq';
        
        switch (operator) {
            case 'eq':
                return columnValue === testValue;
            case 'ne':
                return columnValue !== testValue;
            case 'lt':
                return columnValue < testValue;
            case 'lte':
                return columnValue <= testValue;
            case 'gt':
                return columnValue > testValue;
            case 'gte':
                return columnValue >= testValue;
            case 'contains':
                return String(columnValue).toLowerCase().includes(String(testValue).toLowerCase());
            case 'startsWith':
                return String(columnValue).toLowerCase().startsWith(String(testValue).toLowerCase());
            case 'endsWith':
                return String(columnValue).toLowerCase().endsWith(String(testValue).toLowerCase());
            case 'in':
                return Array.isArray(testValue) && testValue.includes(columnValue);
            case 'notIn':
                return Array.isArray(testValue) && !testValue.includes(columnValue);
            default:
                return false;
        }
    };

    const getRowClass = (rowData) => {
        const classes = {};
        
        // Apply conditional styles
        if (conditionalStyleConfig.value.conditionalStyles.length > 0) {
            // Sort styles by priority (1 = highest priority, 9999 = default)
            const sortedStyles = [...conditionalStyleConfig.value.conditionalStyles].sort((a, b) => {
                const priorityA = a.priority || 9999;
                const priorityB = b.priority || 9999;
                return priorityA - priorityB;
            });
            
            // Apply styles in priority order (lowest priority first, highest priority last to override)
            for (let i = sortedStyles.length - 1; i >= 0; i--) {
                const style = sortedStyles[i];
                if (evaluateCondition(rowData, style) && style.cssClasses) {
                    // Split multiple classes and add them to the classes object
                    const classNames = style.cssClasses.split(' ').filter(cls => cls.trim());
                    classNames.forEach(className => {
                        classes[className.trim()] = true;
                    });
                }
            }
        }
        
        return classes;
    };

    const getRowStyle = (rowData) => {
        let styleObject = {};
        
        // Apply conditional styles
        if (conditionalStyleConfig.value.conditionalStyles.length > 0) {
            // Sort styles by priority
            const sortedStyles = [...conditionalStyleConfig.value.conditionalStyles].sort((a, b) => {
                const priorityA = a.priority || 9999;
                const priorityB = b.priority || 9999;
                return priorityA - priorityB;
            });
            
            // Apply styles in priority order
            for (let i = sortedStyles.length - 1; i >= 0; i--) {
                const style = sortedStyles[i];
                if (evaluateCondition(rowData, style)) {
                    // Apply styleObject if provided
                    if (style.styleObject) {
                        styleObject = { ...styleObject, ...style.styleObject };
                    }
                    
                    // Parse inline styles if provided
                    if (style.inlineStyles) {
                        const parsedStyles = parseInlineStyles(style.inlineStyles);
                        styleObject = { ...styleObject, ...parsedStyles };
                    }
                }
            }
        }
        
        return styleObject;
    };

    const parseInlineStyles = (inlineStyles) => {
        const styles = {};
        
        if (!inlineStyles) return styles;
        
        const declarations = inlineStyles.split(';').filter(decl => decl.trim());
        
        for (const declaration of declarations) {
            const colonIndex = declaration.indexOf(':');
            if (colonIndex !== -1) {
                const property = declaration.substring(0, colonIndex).trim();
                const value = declaration.substring(colonIndex + 1).trim();
                
                // Convert kebab-case to camelCase
                const camelCaseProperty = property.replace(/-([a-z])/g, (match, letter) => letter.toUpperCase());
                styles[camelCaseProperty] = value;
            }
        }
        
        return styles;
    };

    return {
        conditionalStyleConfig,
        getRowClass,
        getRowStyle,
        evaluateCondition
    };
}