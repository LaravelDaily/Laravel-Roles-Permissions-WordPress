<x-layouts.app>
    <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        <div class="p-6">
            <form action="{{ route('posts.store') }}" method="POST">
                @csrf
                <!-- Form Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <h3
                            class="text-lg font-medium text-gray-800 dark:text-gray-200 border-b border-gray-200 dark:border-gray-700 pb-2">
                            Create Post</h3>

                        <div>
                            <x-forms.input label="Title" name="title" placeholder="Enter title" />
                        </div>
                        <div>
                            <x-forms.input label="Slug" name="slug" placeholder="Enter slug" />
                        </div>
                        <div>
                            <x-forms.textarea label="Content" name="content" placeholder="Enter content" />
                        </div>
                        @can('publish', \App\Models\Post::class)
                            <div>
                                <x-forms.checkbox label="Published" name="is_published" :checked="true" value="1" />
                            </div>
                        @endcan
                        <div>
                            <x-forms.input label="Meta Title" name="meta_title" placeholder="Enter meta title" />
                        </div>
                        <div>
                            <x-forms.textarea label="Meta Description" name="meta_description"
                                placeholder="Enter meta description" />
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 pt-5 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('posts.index') }}"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <x-buttons.primary type="submit">
                            Create Post
                        </x-buttons.primary>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
