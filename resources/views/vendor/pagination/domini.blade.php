@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem;">
        {{-- Informations de pagination --}}
        <div style="flex: 1;">
            <p style="font-size: 0.875rem; color: #666; margin: 0;">
                Affichage de <span style="font-weight: 600; color: #000000;">{{ $paginator->firstItem() ?? 0 }}</span>
                à <span style="font-weight: 600; color: #000000;">{{ $paginator->lastItem() ?? 0 }}</span>
                sur <span style="font-weight: 600; color: #000000;">{{ $paginator->total() }}</span> résultats
            </p>
        </div>

        {{-- Liens de pagination --}}
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            {{-- Bouton Précédent --}}
            @if ($paginator->onFirstPage())
                <span style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background: #F5F5F5; color: #CCCCCC; border-radius: 8px; font-size: 0.875rem; cursor: not-allowed;">
                    <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Précédent
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background: white; color: #000000; border: 1px solid #E5E5E5; border-radius: 8px; text-decoration: none; font-size: 0.875rem; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#FF0000'; this.style.color='white'; this.style.borderColor='#FF0000';" onmouseout="this.style.background='white'; this.style.color='#000000'; this.style.borderColor='#E5E5E5';">
                    <svg style="width: 16px; height: 16px; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Précédent
                </a>
            @endif

            {{-- Numéros de page --}}
            <div style="display: flex; gap: 0.25rem;">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; color: #999;">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: linear-gradient(135deg, #FF0000, #CC0000); color: white; border-radius: 8px; font-weight: 700; font-size: 0.875rem;">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: white; color: #000000; border: 1px solid #E5E5E5; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.875rem; transition: all 0.2s;" onmouseover="this.style.background='#CC0000'; this.style.color='white'; this.style.borderColor='#CC0000';" onmouseout="this.style.background='white'; this.style.color='#000000'; this.style.borderColor='#E5E5E5';">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Bouton Suivant --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background: white; color: #000000; border: 1px solid #E5E5E5; border-radius: 8px; text-decoration: none; font-size: 0.875rem; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#FF0000'; this.style.color='white'; this.style.borderColor='#FF0000';" onmouseout="this.style.background='white'; this.style.color='#000000'; this.style.borderColor='#E5E5E5';">
                    Suivant
                    <svg style="width: 16px; height: 16px; margin-left: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background: #F5F5F5; color: #CCCCCC; border-radius: 8px; font-size: 0.875rem; cursor: not-allowed;">
                    Suivant
                    <svg style="width: 16px; height: 16px; margin-left: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
