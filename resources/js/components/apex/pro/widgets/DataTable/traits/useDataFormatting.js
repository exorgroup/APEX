// resources/js/components/apex/pro/widgets/DataTable/traits/useDataFormatting.js

import { computed } from 'vue';

export function useDataFormatting(props) {
    // Format cell value based on data type
    const formatCellValue = (value, column) => {
        if (value === null || value === undefined) return '';
        
        let formattedValue = '';
        
        switch (column.dataType) {
            case 'currency':
                const decimals = typeof column.format === 'number' ? column.format : 2;
                formattedValue = parseFloat(value).toLocaleString('en-US', {
                    style: 'currency',
                    currency: 'USD',
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                });
                break;
                
            case 'percentage':
                const percentDecimals = typeof column.format === 'number' ? column.format : 2;
                formattedValue = `${parseFloat(value).toFixed(percentDecimals)}%`;
                break;
                
            case 'number':
                const numberDecimals = typeof column.format === 'number' ? column.format : 0;
                formattedValue = parseFloat(value).toFixed(numberDecimals);
                break;
                
            case 'shortdate':
                formattedValue = formatDate(value, 'short', column.format);
                break;
                
            case 'longdate1':
                formattedValue = formatDate(value, 'long1', column.format);
                break;
                
            case 'longdate2':
                formattedValue = formatDate(value, 'long2', column.format);
                break;
                
            case 'time':
                formattedValue = formatTime(value, column.format);
                break;
                
            case 'shortdatetime':
                formattedValue = formatDateTime(value, 'short', column.format);
                break;
                
            case 'longdate1time':
                formattedValue = formatDateTime(value, 'long1', column.format);
                break;
                
            case 'longdate2time':
                formattedValue = formatDateTime(value, 'long2', column.format);
                break;
                
            default:
                formattedValue = String(value);
        }
        
        // Add lead and trail text
        return `${column.leadText || ''}${formattedValue}${column.trailText || ''}`;
    };

    // Format date based on culture
    const formatDate = (value, style, culture) => {
        const date = new Date(value);
        if (isNaN(date.getTime())) return String(value);
        
        const cultureSetting = culture || 'US';
        
        if (style === 'short') {
            switch (cultureSetting) {
                case 'EU':
                    return date.toLocaleDateString('en-GB'); // DD/MM/YYYY
                case 'Asia':
                    return date.toLocaleDateString('zh-CN'); // YYYY/MM/DD
                default: // US
                    return date.toLocaleDateString('en-US'); // MM/DD/YYYY
            }
        } else if (style === 'long1') {
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            return date.toLocaleDateString('en-US', options); // Oct 12, 2020
        } else { // long2
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            return date.toLocaleDateString('en-US', options); // October 12, 2020
        }
    };

    // Format time
    const formatTime = (value, format) => {
        const date = new Date(value);
        if (isNaN(date.getTime())) return String(value);
        
        const is24Hour = format === '24';
        const options = {
            hour: 'numeric',
            minute: '2-digit',
            hour12: !is24Hour
        };
        
        return date.toLocaleTimeString('en-US', options);
    };

    // Format date and time
    const formatDateTime = (value, dateStyle, format) => {
        const parts = format?.split('-') || ['US', '12'];
        const culture = parts[0];
        const timeFormat = parts[1];
        
        const dateStr = formatDate(value, dateStyle, culture);
        const timeStr = formatTime(value, timeFormat);
        
        return `${dateStr} ${timeStr}`;
    };

    return {
        formatCellValue,
        formatDate,
        formatTime,
        formatDateTime
    };
}