#!/bin/bash

OUTPUT_FILE="migrations_detailed_columns.txt"

echo "=========================================" > $OUTPUT_FILE
echo "DETAILED MIGRATION COLUMN ANALYSIS" >> $OUTPUT_FILE
echo "Generated: $(date)" >> $OUTPUT_FILE
echo "=========================================" >> $OUTPUT_FILE
echo "" >> $OUTPUT_FILE

counter=1
for file in $(ls -1 database/migrations/*.php | sort); do
    filename=$(basename "$file")
    
    echo "---------------------------------------------------" >> $OUTPUT_FILE
    echo "[$counter] File: $filename" >> $OUTPUT_FILE
    echo "---------------------------------------------------" >> $OUTPUT_FILE
    
    # Extract table name
    table_name=$(grep -o "Schema::create(['\"][^'\"]*['\"]" "$file" | head -1 | sed "s/Schema::create(['\"]//g" | sed "s/['\"].*//g")
    if [ -z "$table_name" ]; then
        table_name=$(grep -o "Schema::table(['\"][^'\"]*['\"]" "$file" | head -1 | sed "s/Schema::table(['\"]//g" | sed "s/['\"].*//g")
        if [ -n "$table_name" ]; then
            echo "Table: $table_name (ALTER TABLE)" >> $OUTPUT_FILE
        fi
    else
        echo "Table: $table_name" >> $OUTPUT_FILE
    fi
    
    echo "" >> $OUTPUT_FILE
    
    # Extract all column definitions
    echo "COLUMN DEFINITIONS:" >> $OUTPUT_FILE
    echo "-------------------" >> $OUTPUT_FILE
    
    # Extract each column with its type and modifiers
    grep -E "\$table->(string|text|integer|bigInteger|unsignedBigInteger|foreignId|enum|boolean|date|timestamp|decimal|char|json|longText|mediumText|rememberToken|id|timestamps|softDeletes)" "$file" | \
    grep -v "Schema::" | \
    while read line; do
        # Clean up the line
        clean_line=$(echo "$line" | sed 's/^\s*//g' | sed 's/\$table->//g' | sed 's/;//g')
        echo "  - $clean_line" >> $OUTPUT_FILE
    done
    
    echo "" >> $OUTPUT_FILE
    
    # Extract foreign key constraints
    fk_count=$(grep -c "constrained\|references" "$file" 2>/dev/null || echo "0")
    if [ "$fk_count" -gt 0 ]; then
        echo "FOREIGN KEY CONSTRAINTS:" >> $OUTPUT_FILE
        echo "-----------------------" >> $OUTPUT_FILE
        grep -E "constrained|references" "$file" | \
        grep -v "//" | \
        while read line; do
            clean_line=$(echo "$line" | sed 's/^\s*//g' | sed 's/;//g')
            echo "  - $clean_line" >> $OUTPUT_FILE
        done
        echo "" >> $OUTPUT_FILE
    fi
    
    # Extract unique constraints
    unique_count=$(grep -c "\$table->unique" "$file" 2>/dev/null || echo "0")
    if [ "$unique_count" -gt 0 ]; then
        echo "UNIQUE CONSTRAINTS:" >> $OUTPUT_FILE
        echo "------------------" >> $OUTPUT_FILE
        grep "\$table->unique" "$file" | \
        grep -v "//" | \
        while read line; do
            clean_line=$(echo "$line" | sed 's/^\s*//g' | sed 's/;//g')
            echo "  - $clean_line" >> $OUTPUT_FILE
        done
        echo "" >> $OUTPUT_FILE
    fi
    
    # Extract indexes
    index_count=$(grep -c "\$table->index" "$file" 2>/dev/null || echo "0")
    if [ "$index_count" -gt 0 ]; then
        echo "INDEXES:" >> $OUTPUT_FILE
        echo "--------" >> $OUTPUT_FILE
        grep "\$table->index" "$file" | \
        grep -v "//" | \
        while read line; do
            clean_line=$(echo "$line" | sed 's/^\s*//g' | sed 's/;//g')
            echo "  - $clean_line" >> $OUTPUT_FILE
        done
        echo "" >> $OUTPUT_FILE
    fi
    
    echo "" >> $OUTPUT_FILE
    ((counter++))
done

echo "=========================================" >> $OUTPUT_FILE
echo "SUMMARY" >> $OUTPUT_FILE
echo "=========================================" >> $OUTPUT_FILE
echo "Total Migration Files: $((counter-1))" >> $OUTPUT_FILE
echo "Generated on: $(date)" >> $OUTPUT_FILE
echo "=========================================" >> $OUTPUT_FILE

echo "Detailed output saved to: $OUTPUT_FILE"
