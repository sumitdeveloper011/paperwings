@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-eye"></i>
                    Email Templates Preview
                </h1>
                <p class="page-header__subtitle">Preview all email templates with sample data</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Templates</span>
                </a>
            </div>
        </div>
    </div>

    <div class="modern-card">
        <div class="modern-card__header">
            <h3 class="modern-card__title">
                <i class="fas fa-info-circle"></i>
                Preview Information
            </h3>
            <p class="modern-card__subtitle">
                All templates are displayed with sample data. Use this page to verify layouts and styling.
            </p>
        </div>
    </div>

    @php
        $currentCategory = null;
        $categoryCount = 0;
    @endphp

    @foreach($previews as $index => $preview)
        @php
            $template = $preview['template'];
            if ($currentCategory !== $template->category) {
                $currentCategory = $template->category;
                $categoryCount++;
            }
        @endphp

        @if($currentCategory !== $template->category || $index === 0)
            @if($index > 0)
                </div>
            @endif
            <div class="modern-card" style="margin-top: {{ $index > 0 ? '2rem' : '1.5rem' }};">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-folder"></i>
                        {{ ucfirst($template->category) }} Templates
                    </h3>
                </div>
            </div>
        @endif

        <div class="modern-card" style="margin-top: 1.5rem;">
            <div class="modern-card__header">
                <div class="modern-card__header-content">
                    <h3 class="modern-card__title">
                        <i class="fas fa-envelope"></i>
                        {{ $template->name }}
                        <span class="badge badge-{{ $template->is_active ? 'success' : 'secondary' }}" style="margin-left: 0.5rem;">
                            {{ $template->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </h3>
                    <p class="modern-card__subtitle">
                        <strong>Subject:</strong> {{ $preview['subject'] }} | 
                        <strong>Slug:</strong> <code>{{ $template->slug }}</code>
                    </p>
                </div>
                <div class="modern-card__header-actions">
                    <a href="{{ route('admin.email-templates.show', $template) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    @can('email-templates.edit')
                    <a href="{{ route('admin.email-templates.edit', $template) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    @endcan
                </div>
            </div>
            <div class="modern-card__body">
                <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                    <strong>Template Variables Used:</strong>
                    <div style="margin-top: 0.5rem; display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        @foreach($preview['variables'] as $key => $value)
                            <span class="badge badge-info" title="{{ $value }}">
                                {{ $key }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div style="border: 2px solid #e0e0e0; border-radius: 8px; overflow: hidden; background: #ffffff;">
                    <div style="background: #f5f5f5; padding: 0.75rem 1rem; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">
                        <strong><i class="fas fa-envelope-open"></i> Email Preview</strong>
                        <a href="{{ route('admin.email-templates.preview-browser', $template) }}?{{ http_build_query($preview['variables']) }}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-primary"
                           style="margin: 0;">
                            <i class="fas fa-external-link-alt"></i> Open in New Tab
                        </a>
                    </div>
                    <div style="padding: 20px; max-height: 600px; overflow-y: auto; background: #f5f5f5;">
                        <div style="background: #ffffff; margin: 0 auto; max-width: 600px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 1px solid #e0e0e0;">
                            <div class="email-preview-wrapper" style="isolation: isolate;">
                                @php
                                    // Use the same logic as previewInBrowser but render directly
                                    $bodyContent = $preview['body'];
                                    
                                    // Extract content using the same method as template-body.blade.php
                                    if (!empty($bodyContent)) {
                                        $isFullHtml = stripos($bodyContent, '<!DOCTYPE') !== false || 
                                                     stripos($bodyContent, '<html') !== false ||
                                                     stripos($bodyContent, '<body') !== false;
                                        
                                        if ($isFullHtml) {
                                            // Extract content between <body> tags
                                            if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $bodyContent, $bodyMatches)) {
                                                $bodyContent = trim($bodyMatches[1]);
                                            }
                                            
                                            // Find the main content table
                                            if (preg_match('/<table[^>]*width[^>]*600[^>]*>(.*?)<\/table>/is', $bodyContent, $tableMatches)) {
                                                $innerContent = $tableMatches[1];
                                            } else {
                                                if (preg_match('/<table[^>]*>(.*?)<\/table>/is', $bodyContent, $tableMatches)) {
                                                    $innerContent = $tableMatches[1];
                                                } else {
                                                    $innerContent = $bodyContent;
                                                }
                                            }
                                            
                                            // Extract <tr> rows and filter
                                            // Use a more robust method that handles nested tables
                                            $rows = [];
                                            $pos = 0;
                                            $innerContentLength = strlen($innerContent);
                                            
                                            while ($pos < $innerContentLength) {
                                                // Find next <tr> tag
                                                $trStart = stripos($innerContent, '<tr', $pos);
                                                if ($trStart === false) {
                                                    break;
                                                }
                                                
                                                // Find matching </tr> tag, accounting for nested tables
                                                $depth = 0;
                                                $trEnd = $trStart;
                                                $searchPos = $trStart + 3;
                                                
                                                while ($searchPos < $innerContentLength) {
                                                    $nextTr = stripos($innerContent, '<tr', $searchPos);
                                                    $nextTrClose = stripos($innerContent, '</tr>', $searchPos);
                                                    
                                                    if ($nextTrClose === false) {
                                                        break;
                                                    }
                                                    
                                                    // If we find a </tr> before another <tr>, and depth is 0, we found our match
                                                    if ($nextTr === false || $nextTrClose < $nextTr) {
                                                        if ($depth === 0) {
                                                            $trEnd = $nextTrClose + 5;
                                                            break;
                                                        }
                                                        $depth--;
                                                        $searchPos = $nextTrClose + 5;
                                                    } else {
                                                        // Found another <tr> before </tr>, increase depth
                                                        $depth++;
                                                        $searchPos = $nextTr + 3;
                                                    }
                                                }
                                                
                                                if ($trEnd > $trStart) {
                                                    $row = substr($innerContent, $trStart, $trEnd - $trStart);
                                                    $rows[] = $row;
                                                    $pos = $trEnd;
                                                } else {
                                                    break;
                                                }
                                            }
                                            
                                            if (empty($rows)) {
                                                // Fallback to regex if manual extraction fails
                                                if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $innerContent, $rowMatches)) {
                                                    $rows = $rowMatches[0];
                                                }
                                            }
                                            
                                            $filteredRows = array_filter($rows, function($row) {
                                                    $lowerRow = strtolower($row);
                                                    
                                                    // Skip if row contains full HTML document structure
                                                    $hasNestedHtml = stripos($row, '<!DOCTYPE') !== false || 
                                                                     stripos($row, '<html') !== false ||
                                                                     stripos($row, '<body') !== false;
                                                    
                                                    if ($hasNestedHtml) {
                                                        return false;
                                                    }
                                                    
                                                    // Check for structural rows - be more specific
                                                    // Only filter if the row is PRIMARILY a structural element
                                                    // Don't filter content rows that might mention these words in their content
                                                    $isLogoRow = (stripos($lowerRow, '<img') !== false && stripos($lowerRow, 'logo') !== false) ||
                                                                 stripos($lowerRow, 'alt="company logo"') !== false ||
                                                                 stripos($lowerRow, 'alt=\'company logo\'') !== false;
                                                    
                                                    $isFooterRow = stripos($lowerRow, 'footer') !== false && 
                                                                   (stripos($lowerRow, 'copyright') !== false || 
                                                                    stripos($lowerRow, 'get in touch') !== false ||
                                                                    stripos($lowerRow, 'social media') !== false ||
                                                                    stripos($lowerRow, 'unsubscribe') !== false);
                                                    
                                                    $isTopBar = stripos($lowerRow, 'top bar') !== false && stripos($lowerRow, 'logo') !== false;
                                                    
                                                    $isStructural = $isLogoRow || $isFooterRow || $isTopBar;
                                                    
                                                return !$isStructural;
                                            });
                                            
                                            $bodyContent = !empty($filteredRows) ? implode('', $filteredRows) : implode('', $rows);
                                        } else {
                                            $bodyContent = $innerContent;
                                        }
                                    }
                                    
                                    // Render clean email structure
                                    echo '<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #ffffff; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif;">';
                                    echo '<tr><td align="center">';
                                    echo '<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; width: 100%; background-color: #ffffff;">';
                                    
                                    // Logo
                                    echo '<tr><td style="padding: 20px 40px; text-align: center; background-color: #ffffff;">';
                                    echo '<img src="' . htmlspecialchars($logoUrl) . '" alt="Company Logo" style="max-width: 180px; height: auto; display: block; margin: 0 auto;" />';
                                    echo '</td></tr>';
                                    
                                    // Content rows (filtered, no logo/footer)
                                    echo $bodyContent;
                                    
                                    // Footer
                                    echo view('emails.partials.footer', [
                                        'contactPhone' => $contactPhone,
                                        'contactEmail' => $contactEmail,
                                        'socialLinks' => $socialLinks
                                    ])->render();
                                    
                                    echo '</table>';
                                    echo '</td></tr>';
                                    echo '</table>';
                                @endphp
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 1rem; padding: 1rem; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                    <strong><i class="fas fa-lightbulb"></i> Note:</strong>
                    <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem;">
                        This is a preview with sample data. Actual emails will use real data from your system.
                        @if($template->description)
                            <br><strong>Purpose:</strong> {{ $template->description }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endforeach

    @if(count($previews) === 0)
        <div class="modern-card">
            <div class="modern-card__body" style="text-align: center; padding: 3rem;">
                <i class="fas fa-inbox fa-3x" style="color: #ccc; margin-bottom: 1rem;"></i>
                <p style="color: #666;">No email templates found.</p>
            </div>
        </div>
    @endif

    <div class="modern-card" style="margin-top: 2rem;">
        <div class="modern-card__body" style="text-align: center; padding: 1.5rem;">
            <p style="margin: 0; color: #666;">
                <i class="fas fa-info-circle"></i>
                Total: <strong>{{ count($previews) }}</strong> email template(s) previewed
            </p>
        </div>
    </div>
</div>

<style>
    .badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }
    .badge-success {
        background-color: #28a745;
        color: #fff;
    }
    .badge-secondary {
        background-color: #6c757d;
        color: #fff;
    }
    .badge-info {
        background-color: #17a2b8;
        color: #fff;
    }
    code {
        background: #f4f4f4;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        font-size: 0.9em;
        font-family: 'Courier New', monospace;
    }
    
    /* Email preview isolation to prevent admin styles from affecting email */
    .email-preview-wrapper {
        isolation: isolate;
    }
    
    .email-preview-wrapper * {
        box-sizing: border-box;
    }
    
    .email-preview-wrapper table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    
    .email-preview-wrapper img {
        max-width: 100% !important;
        height: auto !important;
    }
</style>
@endsection
