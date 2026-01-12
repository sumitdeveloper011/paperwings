@extends('layouts.frontend.main')
@section('content')
    @include('frontend.partials.page-header', [
        'title' => 'Frequently Asked Questions',
        'subtitle' => 'Find answers to common questions about our products and services',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'FAQ', 'url' => null]
        ]
    ])

    <section class="faq-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @if($categories->count() > 0)
                    <div class="faq-filters mb-4">
                        <div class="faq-filter-buttons">
                            <a href="{{ route('faq.index') }}"
                               class="faq-filter-btn {{ !$selectedCategory ? 'active' : '' }}">
                                All Questions
                            </a>
                            @foreach($categories as $category)
                                <a href="{{ route('faq.index', ['category' => $category['value']]) }}"
                                   class="faq-filter-btn {{ $selectedCategory == $category['value'] ? 'active' : '' }}">
                                    {{ $category['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($faqs->count() > 0)
                        <div class="faq-accordion" id="faqAccordion">
                            @foreach($faqs as $index => $faq)
                                <div class="faq-item">
                                    <div class="faq-question"
                                         data-toggle="collapse"
                                         data-target="#faq{{ $faq->id }}"
                                         aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                         aria-controls="faq{{ $faq->id }}"
                                         role="button">
                                        <h3 class="faq-question-text">
                                            <span class="faq-question-number">{{ $index + 1 }}.</span>
                                            {{ $faq->question }}
                                        </h3>
                                        <span class="faq-toggle-icon">
                                            <i class="fas fa-chevron-down"></i>
                                        </span>
                                    </div>
                                    <div id="faq{{ $faq->id }}"
                                         class="collapse {{ $index === 0 ? 'show' : '' }}"
                                         data-parent="#faqAccordion">
                                        <div class="faq-answer">
                                            <div class="faq-answer-content">
                                                @if($faq->answer && trim($faq->answer) !== '')
                                                    {!! nl2br(e($faq->answer)) !!}
                                                @else
                                                    <p class="text-muted">No answer available.</p>
                                                @endif
                                            </div>
                                            @if($faq->category)
                                                <div class="faq-category-badge">
                                                    <i class="fas fa-tag"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $faq->category)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="faq-empty-state text-center py-5">
                            <div class="faq-empty-icon mb-3">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <h3 class="faq-empty-title">No FAQs Available</h3>
                            <p class="faq-empty-text">We're currently updating our FAQ section. Please check back soon or contact us for assistance.</p>
                            <a href="{{ route('contact') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-envelope me-2"></i>Contact Us
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <style>
        .faq-section {
            padding: 60px 0;
            background-color: #f8f9fa;
        }

        .faq-filters {
            text-align: center;
        }

        .faq-filter-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .faq-filter-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ffffff;
            color: #333;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .faq-filter-btn:hover {
            background-color: #f0f0f0;
            border-color: #d0d0d0;
            color: #333;
            text-decoration: none;
        }

        .faq-filter-btn.active {
            background-color: #2c3e50;
            color: #ffffff;
            border-color: #2c3e50;
        }

        .faq-accordion {
            max-width: 900px;
            margin: 0 auto;
        }

        .faq-item {
            background-color: #ffffff;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .faq-question {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .faq-question:hover {
            background-color: #f8f9fa;
        }

        .faq-question[aria-expanded="true"] {
            background-color: #f8f9fa;
        }

        .faq-question-text {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .faq-question-number {
            color: #e95c67;
            font-weight: 700;
            min-width: 25px;
        }

        .faq-toggle-icon {
            color: #e95c67;
            font-size: 14px;
            transition: transform 0.3s ease;
            flex-shrink: 0;
        }

        .faq-question[aria-expanded="true"] .faq-toggle-icon {
            transform: rotate(180deg);
        }

        /* Override conflicting styles from main CSS file */
        .faq-section #faqAccordion .faq-answer {
            max-height: none !important;
            overflow: visible !important;
            padding: 0 25px 25px 25px !important;
            transition: none !important;
            border-top: 1px solid #e9ecef;
        }

        .faq-section #faqAccordion .faq-item.active .faq-answer,
        .faq-section #faqAccordion .faq-item .faq-answer {
            max-height: none !important;
            padding: 0 25px 25px 25px !important;
        }

        .faq-section #faqAccordion .collapse {
            display: none;
        }

        .faq-section #faqAccordion .collapse.show {
            display: block !important;
        }

        .faq-answer-content {
            padding-top: 20px;
            color: #555;
            line-height: 1.8;
            font-size: 15px;
            min-height: 20px;
        }

        .faq-category-badge {
            margin-top: 15px;
            display: inline-block;
            padding: 5px 12px;
            background-color: #e9ecef;
            color: #6c757d;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }

        .faq-category-badge i {
            margin-right: 5px;
            font-size: 10px;
        }

        .faq-empty-state {
            padding: 80px 20px;
        }

        .faq-empty-icon {
            font-size: 64px;
            color: #d0d0d0;
        }

        .faq-empty-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        .faq-empty-text {
            color: #666;
            font-size: 16px;
            max-width: 500px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .faq-section {
                padding: 40px 0;
            }

            .faq-filter-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .faq-filter-btn {
                width: 100%;
                text-align: center;
            }

            .faq-question {
                padding: 15px 20px;
            }

            .faq-question-text {
                font-size: 14px;
            }

            .faq-answer {
                padding: 0 20px 20px 20px;
            }

            .faq-answer-content {
                font-size: 14px;
            }
        }
    </style>

    @push('scripts')
    <script>
        (function() {
            'use strict';

            // Fallback for FAQ collapse if Bootstrap doesn't work
            document.addEventListener('DOMContentLoaded', function() {
                const faqQuestions = document.querySelectorAll('.faq-question');

                faqQuestions.forEach(function(question) {
                    question.addEventListener('click', function(e) {
                        e.preventDefault();
                        const targetId = this.getAttribute('data-target') || this.getAttribute('data-bs-target');
                        if (!targetId) return;

                        const target = document.querySelector(targetId);
                        if (!target) return;

                        const isExpanded = this.getAttribute('aria-expanded') === 'true';
                        const accordion = this.closest('#faqAccordion');

                        // Close other items in accordion
                        if (accordion) {
                            const otherItems = accordion.querySelectorAll('.collapse.show');
                            otherItems.forEach(function(item) {
                                if (item !== target) {
                                    item.classList.remove('show');
                                    const otherQuestion = accordion.querySelector('[data-target="#' + item.id + '"], [data-bs-target="#' + item.id + '"]');
                                    if (otherQuestion) {
                                        otherQuestion.setAttribute('aria-expanded', 'false');
                                    }
                                }
                            });
                        }

                        // Toggle current item
                        if (isExpanded) {
                            target.classList.remove('show');
                            this.setAttribute('aria-expanded', 'false');
                        } else {
                            target.classList.add('show');
                            this.setAttribute('aria-expanded', 'true');
                        }
                    });
                });
            });
        })();
    </script>
    @endpush
@endsection

