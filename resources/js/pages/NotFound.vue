<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center px-4">
      <div class="max-w-lg w-full text-center">
        <!-- 404 Number -->
        <h1 class="text-9xl font-bold text-indigo-600">404</h1>
        
        <!-- Error message -->
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Page Not Found</h2>
        
        <!-- Description -->
        <p class="text-gray-600 mb-8">
          Oops! The page you are looking for might have been removed, had its name changed, 
          or is temporarily unavailable.
        </p>
        
        <!-- Action buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <button 
            @click="goBack" 
            class="px-6 py-3 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors"
          >
            Go Back
          </button>
          <button 
            @click="goHome" 
            class="px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
          >
            Back to Home
          </button>
        </div>
        
        <!-- Illustration -->
        <div class="mt-12">
          <svg class="w-full max-w-sm mx-auto" viewBox="0 0 480 240" xmlns="http://www.w3.org/2000/svg">
            <!-- Lost character -->
            <g transform="translate(240, 180)">
              <!-- Body -->
              <ellipse cx="0" cy="0" rx="40" ry="40" fill="#4F46E5" />
              
              <!-- Face -->
              <circle cx="-15" cy="-10" r="5" fill="white" />
              <circle cx="15" cy="-10" r="5" fill="white" />
              <path d="M -15,15 Q 0,5 15,15" stroke="white" stroke-width="3" fill="none" />
              
              <!-- Lost sign -->
              <rect x="-70" y="-80" width="140" height="50" rx="10" fill="white" />
              <text x="0" y="-45" font-family="Arial" font-size="24" text-anchor="middle" fill="#4F46E5">LOST</text>
            </g>
            
            <!-- Map elements -->
            <g transform="translate(120, 100)">
              <path d="M 0,0 L 30,-30 L 60,0 L 30,30 Z" fill="#A5B4FC" />
              <circle cx="30" cy="0" r="5" fill="#4F46E5" />
            </g>
            
            <g transform="translate(350, 80)">
              <circle cx="0" cy="0" r="25" fill="#A5B4FC" />
              <path d="M -10,-10 L 10,10 M -10,10 L 10,-10" stroke="#4F46E5" stroke-width="3" />
            </g>
            
            <!-- Dotted paths -->
            <path d="M 120,100 Q 180,50 240,120" stroke="#A5B4FC" stroke-width="2" stroke-dasharray="5,5" fill="none" />
            <path d="M 240,120 Q 300,70 350,80" stroke="#A5B4FC" stroke-width="2" stroke-dasharray="5,5" fill="none" />
          </svg>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { useRouter } from 'vue-router';
  
  const router = useRouter();
  
  const goBack = () => {
    router.back();
  };
  
  const goHome = () => {
    router.push('/');
  };
  </script>