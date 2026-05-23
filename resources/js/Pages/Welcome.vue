<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

const plan = ref('pro')
const amount = ref(10000)
const extended = ref(false)

const loading = ref(false)
const result = ref(null)
const error = ref(null)

const isValid = computed(() => {
    return amount.value >= 100 && amount.value <= 5000000
})

const simulate = async () => {
    if (!isValid.value) return

    loading.value = true
    error.value = null
    result.value = null

    try {
        const response = await axios.get('/api/simulate', {
            params: {
                plan: plan.value,
                amount: amount.value,
                mode: extended.value ? 'extended' : null
            }
        })

        result.value = response.data
    } catch (e) {
        error.value = 'Unable to fetch live market price. Please check your connection or try again later.'
    } finally {
        loading.value = false
    }
}
</script>

<template>
<div class="min-h-screen relative overflow-hidden bg-[#0B1120] text-white">

    <!-- BACKGROUND GLOWS -->
    <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-blue-500/20 blur-3xl rounded-full"></div>
    <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-purple-500/20 blur-3xl rounded-full"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 py-10">

        <!-- HERO -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-3 bg-white/10 backdrop-blur-lg border border-white/10 px-5 py-2 rounded-full mb-6">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-sm text-gray-300">LIVE HTX MARKET SIMULATION</span>
            </div>

            <h1 class="text-5xl md:text-6xl font-black tracking-tight">
                Zero Fee
                <span class="bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent">
                    Crypto Trading
                </span>
            </h1>

            <p class="text-gray-400 max-w-2xl mx-auto mt-5 text-lg">
                Simulate HTX broker rebates, platform execution,
                and zero-fee trading economics in real time.
            </p>
        </div>

        <!-- MAIN PANEL -->
        <div class="bg-white/5 backdrop-blur-2xl border border-white/10 rounded-[32px] shadow-2xl p-6 md:p-10">

            <!-- FORM -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- PLAN -->
                <div>
                    <label class="block text-sm text-gray-400 mb-3">Trading Plan</label>
                    <select
                        v-model="plan"
                        :disabled="loading"
                        class="w-full bg-white/10 border border-white/10 rounded-2xl p-4 text-white focus:ring-2 focus:ring-cyan-400"
                    >
                        <option value="pro" class="text-black">Pro (₹199/month – Zero Trading Fees)</option>
                        <option value="free" class="text-black">Free Starter (0.04% per trade)</option>
                    </select>
                </div>

                <!-- AMOUNT -->
                <div>
                    <label class="block text-sm text-gray-400 mb-3">Amount (INR)</label>
                    <input
                        v-model.number="amount"
                        type="number"
                        min="100"
                        max="5000000"
                        placeholder="Enter INR amount"
                        :disabled="loading"
                        class="w-full bg-white/10 border border-white/10 rounded-2xl p-4 text-white placeholder-gray-500 focus:ring-2 focus:ring-cyan-400"
                    />
                    <p v-if="amount < 100 || amount > 5000000" class="text-red-400 text-sm mt-2">
                        Please enter an amount between ₹100 and ₹50,00,000.
                    </p>
                </div>

                <!-- TOGGLE -->
                <div>
                    <label class="block text-sm text-gray-400 mb-3">Extended Calculation</label>
                    <button
                        @click="extended = !extended"
                        :disabled="loading"
                        class="w-full p-4 rounded-2xl font-bold transition-all duration-300 border"
                        :class="extended
                            ? 'bg-gradient-to-r from-cyan-500 to-blue-600 border-cyan-400'
                            : 'bg-white/10 border-white/10 hover:bg-white/20'"
                    >
                        {{ extended ? 'Extended Calculation Enabled' : 'Core Calculation' }}
                    </button>
                </div>
            </div>

            <!-- SIMULATE BUTTON -->
            <div class="mt-8">
                <button
                    @click="simulate"
                    :disabled="loading || !isValid"
                    class="group relative overflow-hidden px-8 py-4 rounded-2xl font-bold text-lg bg-gradient-to-r from-cyan-500 to-blue-600 hover:scale-[1.02] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span class="relative z-10">
                        <span v-if="loading">Fetching live price and calculating…</span>
                        <span v-else>[ SIMULATE ]</span>
                    </span>
                    <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition"></div>
                </button>
            </div>
        </div>

        <!-- LOADING STATE -->
        <div
            v-if="loading"
            class="mt-10 bg-white/5 backdrop-blur-xl border border-white/10 rounded-[28px] p-10 text-center"
        >
            <div class="w-20 h-20 mx-auto border-[5px] border-cyan-400/30 border-t-cyan-400 rounded-full animate-spin"></div>
            <h3 class="text-2xl font-bold mt-6">Fetching live price and calculating…</h3>
            <p class="text-gray-400 mt-2">Connecting to HTX exchange...</p>
        </div>

        <!-- ERROR STATE -->
        <div
            v-if="error && !loading"
            class="mt-8 bg-red-500/10 border border-red-500/20 rounded-3xl p-6 text-center"
        >
            <p class="text-3xl mb-3">⚠️</p>
            <h3 class="text-red-400 font-bold text-lg">{{ error }}</h3>
            <button
                @click="simulate"
                class="mt-4 px-6 py-2 bg-red-500/20 border border-red-500/30 rounded-xl text-red-300 hover:bg-red-500/30 transition"
            >
                Retry
            </button>
        </div>

        <!-- RESULTS -->
        <div v-if="result && !loading" class="mt-10 space-y-8">

            <!-- LIVE PRICE INDICATOR -->
            <div class="text-center">
                <div class="inline-flex items-center gap-2 bg-white/10 border border-white/10 px-4 py-2 rounded-full">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-300">
                        LIVE BTC/USDT PRICE: ₹{{ Number(result.market_price_inr).toLocaleString('en-IN') }} (auto-refresh)
                    </span>
                </div>
            </div>

            <!-- CORE FEE BREAKDOWN -->
            <div class="bg-white/5 border border-white/10 rounded-3xl p-6 md:p-8">
                <h3 class="text-lg font-bold text-gray-300 mb-6">FEE BREAKDOWN</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                        <span class="text-gray-400">HTX Trading Fee (0.1%)</span>
                        <span class="font-bold">₹{{ result.htx_fee_inr }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                        <span class="text-green-400">Broker Rebate (60%)</span>
                        <span class="font-bold text-green-400">−₹{{ result.rebate_inr }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                        <span class="text-gray-400">Net Cost to Platform</span>
                        <span class="font-bold">₹{{ result.net_cost_to_platform }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-white/5">
                        <span class="text-gray-400">What You Pay ({{ plan === 'pro' ? 'Pro plan' : 'Free Starter' }})</span>
                        <span class="font-bold">₹{{ result.customer_pays_fee }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="font-bold text-white">Final Crypto You Receive</span>
                        <span class="font-black text-lg">{{ result.final_crypto_received }} BTC</span>
                    </div>
                </div>
            </div>

            <!-- EXTENDED MODE RESULTS -->
            <div v-if="result.extended_mode" class="space-y-6">
                <h3 class="text-center text-lg font-bold text-gray-300">EXTENDED CALCULATION ANALYSIS</h3>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                    <!-- USER VIEW -->
                    <div class="bg-gradient-to-br from-cyan-500 to-blue-700 rounded-[28px] p-6 shadow-2xl">
                        <div class="mb-5">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-cyan-100">USER VIEW</h4>
                            <p class="text-xs text-cyan-200 mt-1">(What the customer sees)</p>
                        </div>
                        <div class="space-y-4">
                            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-4">
                                <p class="text-cyan-100 text-sm">Displayed Buy Price</p>
                                <h3 class="text-2xl font-black mt-1">₹{{ Number(result.displayed_price_inr).toLocaleString('en-IN') }}/BTC</h3>
                            </div>
                            <div class="bg-white/10 rounded-2xl p-4">
                                <p class="text-cyan-100 text-sm">Amount Used</p>
                                <h3 class="text-xl font-bold mt-1">₹{{ Number(result.amount_inr).toLocaleString('en-IN') }}</h3>
                            </div>
                            <div class="bg-white/10 rounded-2xl p-4">
                                <p class="text-cyan-100 text-sm">Expected Crypto</p>
                                <h3 class="text-lg font-bold mt-1 break-all">{{ result.expected_crypto_received }} BTC</h3>
                            </div>
                            <div class="bg-white/10 rounded-2xl p-4">
                                <p class="text-cyan-100 text-sm">What You Pay</p>
                                <h3 class="text-xl font-bold mt-1">₹{{ result.customer_pays_fee }} ({{ plan === 'pro' ? 'Pro plan' : '0.04% fee' }})</h3>
                            </div>
                        </div>
                    </div>

                    <!-- PLATFORM EXECUTION -->
                    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[28px] p-6">
                        <div class="mb-5">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-gray-400">PLATFORM EXECUTION</h4>
                            <p class="text-xs text-gray-500 mt-1">(Backend reality)</p>
                        </div>
                        <div class="space-y-4">
                            <div class="bg-white/5 rounded-2xl p-4">
                                <p class="text-gray-400 text-sm">True Market Price</p>
                                <h3 class="text-2xl font-black mt-1">₹{{ Number(result.market_price_inr).toLocaleString('en-IN') }}/BTC</h3>
                            </div>
                            <div class="bg-white/5 rounded-2xl p-4">
                                <p class="text-gray-400 text-sm">Amount After Processing</p>
                                <h3 class="text-xl font-bold mt-1">₹{{ Number(result.executable_amount).toLocaleString('en-IN') }} <span class="text-sm text-gray-500">(−₹8 fee)</span></h3>
                            </div>
                            <div class="bg-white/5 rounded-2xl p-4">
                                <p class="text-gray-400 text-sm">Actual Crypto Executed</p>
                                <h3 class="text-lg font-bold mt-1 break-all">{{ result.actual_crypto_executed }} BTC</h3>
                            </div>
                        </div>
                    </div>

                    <!-- PLATFORM REVENUE -->
                    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[28px] p-6">
                        <div class="mb-5">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-green-400">PLATFORM REVENUE</h4>
                        </div>
                        <div class="space-y-4">
                            <div class="bg-white/5 rounded-2xl p-4 flex justify-between items-center">
                                <span class="text-gray-400 text-sm">Processing Fee</span>
                                <span class="font-bold">₹{{ result.processing_fee_inr }}</span>
                            </div>
                            <div class="bg-white/5 rounded-2xl p-4 flex justify-between items-center">
                                <span class="text-gray-400 text-sm">Price Adjustment</span>
                                <span class="font-bold">₹{{ result.price_adjustment_revenue_inr }}</span>
                            </div>
                            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-4 flex justify-between items-center">
                                <span class="font-bold text-green-100">Total Revenue</span>
                                <span class="text-2xl font-black">₹{{ result.total_platform_revenue_inr }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
</template>
