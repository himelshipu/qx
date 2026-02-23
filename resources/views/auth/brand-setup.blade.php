@extends('frontend.layouts.app')

@section('content')
<div  x-data="brandSetup()"   x-init="loadData()"
    class="max-w-2xl mx-auto px-4 sm:px-0 my-10">
    <!-- Top Navigation -->
    <div class="flex items-center justify-between mb-6">
        <button 
            @click="prevStep()" 
            x-show="currentStepIndex > 0"
            class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:underline">
            ← Back
        </button>

        <div class="text-sm text-gray-500 dark:text-gray-400">
            Step <span x-text="currentStepIndex + 1"></span> of <span x-text="steps.length"></span>
        </div>

        <div></div>
    </div>

    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2 mb-10">
        <div 
            class="bg-gradient-to-r from-pink-400 to-pink-600 h-2 rounded-full transition-all duration-300"
            :style="'width: ' + progressPercentage() + '%'"
        ></div>
    </div>

    <!-- Step 1: Objective -->
    <div x-show="currentStep.key === 'objective'" x-cloak class="text-center">
        <h1 class="text-3xl font-bold mb-10">What are you here to do?</h1>

        <div class="space-y-4 mb-10">
            <template x-for="option in currentStep.options">
                <label class="flex items-center p-5 border rounded-2xl cursor-pointer transition-all"
                    :class="formData.objective === option.value ? 'border-pink-400 ring-1 ring-pink-400 bg-pink-50 dark:bg-pink-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'">
                    <input type="radio" x-model="formData.objective" :value="option.value" class="w-4 h-4 rounded-full cursor-pointer">
                    <span class="text-lg font-medium ml-4" x-text="option.label"></span>
                </label>
            </template>
        </div>

        <div class="space-y-3">
            <button @click="nextStep()" 
                class="w-full py-4 rounded-xl font-bold"
                :class="formData.objective ? 'bg-gradient-to-r from-gray-900 to-gray-800 hover:from-pink-600 hover:to-pink-500 text-white' : 'bg-gray-400 text-white cursor-not-allowed'"
                :disabled="!formData.objective">
                Continue
            </button>
            <button @click="skipStep()" class="text-sm text-gray-500 hover:underline">Skip</button>
        </div>
    </div>

    <!-- Step 2: Budget -->
    <div x-show="currentStep.key === 'budget'" x-cloak class="text-center">
        <h1 class="text-3xl font-bold mb-10">What's your approximate budget?</h1>

        <div class="space-y-4 mb-10">
            <template x-for="option in currentStep.options">
                <label class="flex items-center p-5 border rounded-2xl cursor-pointer transition-all"
                    :class="formData.budget === option.value ? 'border-pink-400 ring-1 ring-pink-400 bg-pink-50 dark:bg-pink-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'">
                    <input type="radio" x-model="formData.budget" :value="option.value" class="w-4 h-4 rounded-full cursor-pointer">
                    <span class="text-lg font-medium ml-4" x-text="option.label"></span>
                </label>
            </template>
        </div>

        <div class="space-y-3">
            <button @click="nextStep()" 
                class="w-full py-4 rounded-xl font-bold"
                :class="formData.budget ? 'bg-gradient-to-r from-gray-900 to-gray-800 hover:from-pink-600 hover:to-pink-500 text-white' : 'bg-gray-400 text-white cursor-not-allowed'"
                :disabled="!formData.budget">
                Continue
            </button>
            <button @click="skipStep()" class="text-sm text-gray-500 hover:underline">Skip</button>
        </div>
    </div>

    <!-- Step 3: Business Type -->
    <div x-show="currentStep.key === 'business-type'" x-cloak class="text-center">
        <h1 class="text-3xl font-bold mb-10">What type of business are you?</h1>

        <div class="space-y-4 mb-10">
            <template x-for="option in currentStep.options">
                <label class="flex items-center p-5 border rounded-2xl cursor-pointer transition-all"
                    :class="formData['business-type'] === option.value ? 'border-pink-400 ring-1 ring-pink-400 bg-pink-50 dark:bg-pink-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'">
                    <input type="radio" x-model="formData['business-type']" :value="option.value" class="w-4 h-4 rounded-full cursor-pointer">
                    <span class="text-lg font-medium ml-4" x-text="option.label"></span>
                </label>
            </template>
        </div>

        <div class="space-y-3">
            <button @click="nextStep()" 
                class="w-full py-4 rounded-xl font-bold"
                :class="formData['business-type'] ? 'bg-gradient-to-r from-gray-900 to-gray-800 hover:from-pink-600 hover:to-pink-500 text-white' : 'bg-gray-400 text-white cursor-not-allowed'"
                :disabled="!formData['business-type']">
                Continue
            </button>
            <button @click="skipStep()" class="text-sm text-gray-500 hover:underline">Skip</button>
        </div>
    </div>

    <!-- Step 4: Company Size -->
    <div x-show="currentStep.key === 'company-size'" x-cloak class="text-center">
        <h1 class="text-3xl font-bold mb-10">How many people work at your company?</h1>

        <div class="space-y-4 mb-10">
            <template x-for="option in currentStep.options">
                <label class="flex items-center p-5 border rounded-2xl cursor-pointer transition-all"
                    :class="formData['company-size'] === option.value ? 'border-pink-400 ring-1 ring-pink-400 bg-pink-50 dark:bg-pink-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'">
                    <input type="radio" x-model="formData['company-size']" :value="option.value" class="w-4 h-4 rounded-full cursor-pointer">
                    <span class="text-lg font-medium ml-4" x-text="option.label"></span>
                </label>
            </template>
        </div>

        <div class="space-y-3">
            <button @click="nextStep()" 
                class="w-full py-4 rounded-xl font-bold"
                :class="formData['company-size'] ? 'bg-gradient-to-r from-gray-900 to-gray-800 hover:from-pink-600 hover:to-pink-500 text-white' : 'bg-gray-400 text-white cursor-not-allowed'"
                :disabled="!formData['company-size']">
                Continue
            </button>
            <button @click="skipStep()" class="text-sm text-gray-500 hover:underline">Skip</button>
        </div>
    </div>

    <!-- Step 5: Influencer Type (Checkbox Step) -->
    <div x-show="currentStep.key === 'influencer-type'" x-cloak class="text-center">
        <h1 class="text-3xl font-bold mb-10">What industries are you interested in?</h1>

        <div class="grid grid-cols-2 gap-4 mb-10">
            <template x-for="option in currentStep.options">
                <label class="flex items-center justify-center p-5 border rounded-2xl cursor-pointer transition-all"
                    :class="isInfluencerTypeSelected(option.value) ? 'border-pink-400 ring-1 ring-pink-400 bg-pink-50 dark:bg-pink-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'">
                    <input type="checkbox" @change="toggleInfluencerType(option.value)" :checked="isInfluencerTypeSelected(option.value)" class="w-4 h-4 cursor-pointer">
                    <span class="text-lg font-medium ml-3" x-text="option.label"></span>
                </label>
            </template>
        </div>

        <div class="space-y-3">
            <button @click="nextStep()" 
                class="w-full py-4 rounded-xl font-bold bg-gradient-to-r from-gray-900 to-gray-800 hover:from-pink-600 hover:to-pink-500 text-white">
                Continue
            </button>
            <button @click="skipStep()" class="text-sm text-gray-500 hover:underline">Skip</button>
        </div>
    </div>

    <!-- Step 6: Summary/Complete (No checkbox) -->
    <div x-show="currentStep.key === 'summary'" x-cloak class="text-center">
        <h1 class="text-3xl font-bold mb-10">You're all set!</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-10">Here's a summary of your brand information.</p>

        <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 mb-10 text-left">
            <div class="space-y-4">
                <div v-if="formData.objective">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Objective</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white" x-text="getOptionLabel('objective', formData.objective)"></p>
                </div>
                <div v-if="formData.budget">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Budget</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white" x-text="getOptionLabel('budget', formData.budget)"></p>
                </div>
                <div v-if="formData['business-type']">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Business Type</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white" x-text="getOptionLabel('business-type', formData['business-type'])"></p>
                </div>
                <div v-if="formData['company-size']">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Company Size</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white" x-text="getOptionLabel('company-size', formData['company-size'])"></p>
                </div>
                <div v-if="formData['influencer-type'] && formData['influencer-type'].length > 0">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Interested Industries</p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <template x-for="type in formData['influencer-type']">
                            <span class="inline-block bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 px-3 py-1 rounded-full text-sm" x-text="type"></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <button @click="completeBrandSetup()" class="w-full py-4 rounded-xl font-bold bg-gradient-to-r from-gray-900 to-gray-800 hover:from-pink-600 hover:to-pink-500 text-white">
                Go to Dashboard
            </button>
        </div>
    </div>

</div>

<script>
function brandSetup() {
    return {
        currentStepIndex: 0,
        formData: {
            objective: '',
            budget: '',
            'business-type': '',
            'company-size': '',
            'influencer-type': []
        },
        steps: [
            {
                key: 'objective',
                options: [
                    { value: 'one-time-campaign', label: 'Find influencers for a one-time campaign' },
                    { value: 'ongoing-content', label: 'Get ongoing influencer content' },
                    { value: 'exploring', label: 'I\'m not sure yet, just exploring' }
                ]
            },
            {
                key: 'budget',
                options: [
                    { value: 'under-1000', label: 'Under $1,000' },
                    { value: '1000-5000', label: '$1,000 - $5,000' },
                    { value: '5000-10000', label: '$5,000 - $10,000' },
                    { value: '10000-25000', label: '$10,000 - $25,000' },
                    { value: '25000-50000', label: '$25,000 - $50,000' },
                    { value: '50000-plus', label: '$50,000+' }
                ]
            },
            {
                key: 'business-type',
                options: [
                    { value: 'agency', label: 'Agency' },
                    { value: 'ecommerce', label: 'E-commerce' },
                    { value: 'saas', label: 'SaaS/Software' },
                    { value: 'local', label: 'Local Business' },
                    { value: 'other', label: 'Other' }
                ]
            },
            {
                key: 'company-size',
                options: [
                    { value: 'just-me', label: 'Just me' },
                    { value: '2-10', label: '2-10 people' },
                    { value: '11-50', label: '11-50 people' },
                    { value: '51-200', label: '51-200 people' },
                    { value: '201-500', label: '201-500 people' },
                    { value: '500-plus', label: '500+ people' }
                ]
            },
            {
                key: 'influencer-type',
                options: [
                    { value: 'beauty', label: 'Beauty' },
                    { value: 'fashion', label: 'Fashion' },
                    { value: 'travel', label: 'Travel' },
                    { value: 'health-fitness', label: 'Health & Fitness' },
                    { value: 'food', label: 'Food' },
                    { value: 'tech', label: 'Technology' },
                    { value: 'gaming', label: 'Gaming' },
                    { value: 'lifestyle', label: 'Lifestyle' }
                ]
            },
            {
                key: 'summary',
                options: []
            }
        ],

        get currentStep() {
            return this.steps[this.currentStepIndex];
        },

        progressPercentage() {
            return ((this.currentStepIndex + 1) / this.steps.length) * 100;
        },

        nextStep() {
            this.saveCurrentStep();
            if (this.currentStepIndex < this.steps.length - 1) {
                this.currentStepIndex++;
            }
        },

        prevStep() {
            if (this.currentStepIndex > 0) {
                this.currentStepIndex--;
            }
        },

        skipStep() {
            // Mark current step as skipped (null) and move to next
            this.formData[this.currentStep.key] = null;
            this.nextStep();
        },

        saveCurrentStep() {
            const stepKey = this.currentStep.key;
            const value = this.formData[stepKey];
            
            fetch('{{ route("brand-setup.store-step") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    step: stepKey,
                    value: value
                })
            }).catch(error => console.error('Error saving step:', error));
        },

        loadData() {
            fetch('{{ route("brand-setup.get-data") }}')
                .then(response => response.json())
                .then(data => {
                    const setupData = data.setup_data;
                    if (setupData) {
                        this.formData = {
                            ...this.formData,
                            ...setupData
                        };
                        // Ensure influencer-type is an array
                        if (typeof this.formData['influencer-type'] === 'string') {
                            this.formData['influencer-type'] = [];
                        } else if (!Array.isArray(this.formData['influencer-type'])) {
                            this.formData['influencer-type'] = [];
                        }
                    }
                })
                .catch(error => console.error('Error loading data:', error));
        },

        isInfluencerTypeSelected(value) {
            return Array.isArray(this.formData['influencer-type']) && 
                   this.formData['influencer-type'].includes(value);
        },

        toggleInfluencerType(value) {
            if (!Array.isArray(this.formData['influencer-type'])) {
                this.formData['influencer-type'] = [];
            }
            
            if (this.isInfluencerTypeSelected(value)) {
                this.formData['influencer-type'] = this.formData['influencer-type'].filter(t => t !== value);
            } else {
                this.formData['influencer-type'].push(value);
            }
        },

        getOptionLabel(stepKey, value) {
            const step = this.steps.find(s => s.key === stepKey);
            if (!step) return value;
            
            const option = step.options.find(o => o.value === value);
            return option ? option.label : value;
        },

        completeBrandSetup() {
            fetch('{{ route("brand-setup.complete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                const ct = response.headers.get('content-type') || '';
                if (ct.includes('application/json')) {
                    return response.json().then(data => {
                        if (data && data.success) {
                            window.location.href = data.redirect;
                        }
                    });
                }

                // Non-JSON response (HTML error/redirect) — fallback to dashboard
                window.location.href = '{{ route("dashboard.index") }}';
            })
            .catch(error => {
                console.error('Error completing setup:', error);
                window.location.href = '{{ route("dashboard.index") }}';
            });
        }
    }
}
</script>

@endsection