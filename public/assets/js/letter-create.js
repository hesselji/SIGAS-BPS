/**
 * SIGAS-BPS - Letter Create Form JavaScript
 * Handles KKA cascade loading, search, and form interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    const archiveTypeSelect = document.getElementById('archiveType');
    const classificationParent = document.getElementById('classificationParent');
    const classificationChild = document.getElementById('classificationChild');
    const scopeCard = document.getElementById('scopeCard');
    const scopePreview = document.getElementById('scopePreview');
    const usesBudgetSelect = document.getElementById('usesBudget');
    const kkaSearchField = document.getElementById('kkaSearchField');
    const kkaSearchClear = document.getElementById('kkaSearchClear');
    
    let allClassificationItems = [];
    let debounceTimer;

    // 1. Load classification groups when archive type changes
    if (archiveTypeSelect) {
        archiveTypeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const archiveTypeName = selectedOption.text;
            
            // Show Master Klasifikasi card
            if (scopeCard && scopePreview) {
                scopeCard.style.display = 'block';
                scopePreview.textContent = archiveTypeName.toUpperCase();
            }
            
            loadClassificationGroups(this.value);
        });
        
        // Trigger initial load if there's an old value
        if (archiveTypeSelect.value) {
            archiveTypeSelect.dispatchEvent(new Event('change'));
        }
    }
    
    // 2. Load classification items when parent group changes
    if (classificationParent) {
        classificationParent.addEventListener('change', function() {
            loadClassificationItems(this.value);
        });
    }

    // 3. KKA Search functionality
    if (kkaSearchField && classificationChild) {
        kkaSearchField.addEventListener('input', function() {
            const keyword = this.value.trim().toLowerCase();
            
            // Show/hide clear button
            if (kkaSearchClear) {
                kkaSearchClear.style.display = keyword.length > 0 ? 'grid' : 'none';
            }
            
            if (keyword.length < 2) {
                // Show all items if search is too short
                showAllClassificationItems();
                return;
            }
            
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                filterClassificationItems(keyword);
            }, 200);
        });
        
        // Clear search button
        if (kkaSearchClear) {
            kkaSearchClear.addEventListener('click', function() {
                kkaSearchField.value = '';
                kkaSearchClear.style.display = 'none';
                showAllClassificationItems();
                kkaSearchField.focus();
            });
        }
    }

    // 4. Auto-fill: Jika Ada Anggaran = Ya, otomatis pilih Fasilitatif & KU
    if (usesBudgetSelect && archiveTypeSelect && classificationParent) {
        usesBudgetSelect.addEventListener('change', function() {
            if (this.value === 'Y') {
                setSelectValue(archiveTypeSelect, 'FASILITATIF');
                
                setTimeout(() => {
                    setSelectValue(classificationParent, 'KU');
                    classificationParent.dispatchEvent(new Event('change'));
                }, 100);
            }
        });
    }
    
    /**
     * Load classification groups based on archive type
     */
    async function loadClassificationGroups(archiveType) {
        if (!classificationParent) return;
        
        // Reset child dropdown
        classificationChild.innerHTML = '<option value="">Pilih kelompok KKA dahulu</option>';
        classificationChild.disabled = true;
        
        if (!archiveType) {
            classificationParent.innerHTML = '<option value="">Pilih jenis arsip dahulu</option>';
            classificationParent.disabled = true;
            if (scopeCard) scopeCard.style.display = 'none';
            return;
        }
        
        try {
            const response = await fetch(`/api/classification-groups?archive_type=${encodeURIComponent(archiveType)}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const groups = await response.json();
            
            // Check if groups is an array
            if (!Array.isArray(groups) || groups.length === 0) {
                classificationParent.innerHTML = '<option value="">Tidak ada kelompok tersedia</option>';
                classificationParent.disabled = true;
                console.warn('No classification groups found for:', archiveType);
                return;
            }
            
            const options = groups.map(g => 
                `<option value="${escapeHtml(g.code)}">${escapeHtml(g.code)} — ${escapeHtml(g.name)}</option>`
            ).join('');
            
            classificationParent.innerHTML = '<option value="">Pilih kelompok KKA</option>' + options;
            classificationParent.disabled = false;
            
            // Restore old value if exists
            const oldValue = classificationParent.dataset.old;
            if (oldValue && groups.find(g => g.code === oldValue)) {
                classificationParent.value = oldValue;
                loadClassificationItems(oldValue);
            }
        } catch (error) {
            console.error('Error loading classification groups:', error);
            classificationParent.innerHTML = '<option value="">Gagal memuat data</option>';
            classificationParent.disabled = true;
        }
    }
    
    /**
     * Load classification items based on group
     */
    async function loadClassificationItems(groupCode) {
        if (!classificationChild || !groupCode) {
            if (classificationChild) {
                classificationChild.innerHTML = '<option value="">Pilih kelompok KKA dahulu</option>';
                classificationChild.disabled = true;
            }
            return;
        }
        
        try {
            const response = await fetch(`/api/classification-items?group=${encodeURIComponent(groupCode)}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const items = await response.json();
            
            allClassificationItems = Array.isArray(items) ? items : [];
            
            if (allClassificationItems.length === 0) {
                classificationChild.innerHTML = '<option value="">Tidak ada klasifikasi tersedia</option>';
                classificationChild.disabled = true;
                return;
            }
            
            renderClassificationDropdown(allClassificationItems);
            classificationChild.disabled = false;
            
            // Restore old value if exists
            const oldValue = classificationChild.dataset.old;
            if (oldValue && allClassificationItems.find(i => i.code === oldValue)) {
                classificationChild.value = oldValue;
            }
        } catch (error) {
            console.error('Error loading classification items:', error);
            classificationChild.innerHTML = '<option value="">Gagal memuat data</option>';
            classificationChild.disabled = true;
        }
    }
    
    /**
     * Render classification dropdown with all items
     */
    function renderClassificationDropdown(items) {
        if (!classificationChild) return;
        
        const options = items.map(item => 
            `<option value="${escapeHtml(item.code)}">${escapeHtml(item.code)} — ${escapeHtml(item.name)}</option>`
        ).join('');
        
        classificationChild.innerHTML = '<option value="">Pilih kode klasifikasi</option>' + options;
    }
    
    /**
     * Show all classification items (clear filter)
     */
    function showAllClassificationItems() {
        if (allClassificationItems.length > 0) {
            renderClassificationDropdown(allClassificationItems);
        }
    }
    
    /**
     * Filter classification items based on search keyword
     */
    function filterClassificationItems(keyword) {
        if (!classificationChild || allClassificationItems.length === 0) return;
        
        const filtered = allClassificationItems.filter(item => {
            const code = item.code.toLowerCase();
            const name = (item.name || '').toLowerCase();
            const rawCode = (item.raw_code || '').toLowerCase();
            
            return code.includes(keyword) || 
                   name.includes(keyword) || 
                   rawCode.includes(keyword);
        });
        
        if (filtered.length === 0) {
            classificationChild.innerHTML = '<option value="">Tidak ada hasil ditemukan</option>';
        } else {
            const options = filtered.map(item => 
                `<option value="${escapeHtml(item.code)}">${escapeHtml(item.code)} — ${escapeHtml(item.name)}</option>`
            ).join('');
            
            classificationChild.innerHTML = '<option value="">Pilih kode klasifikasi</option>' + options;
        }
    }
    
    /**
     * Helper: Set select value if option exists
     */
    function setSelectValue(select, value) {
        const option = Array.from(select.options).find(opt => opt.value === value);
        if (option) {
            select.value = value;
        }
    }
    
    /**
     * Helper: Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});